<?php
// Idempotent CLI: lay out the REAL Aurora 3-column dashboard block set on the
// default dashboard page (my_pages id=2, pagetypepattern 'my-index',
// subpagepattern '2', parentcontextid 1 = system context).
//
//   content  (middle): iomad_learningpath, lp (Learning plans), myoverview
//   side-pre (rail)  : learningpath, timeline, calendar_month,
//                      recentlyaccessedcourses, badges, completion_progress,
//                      recentlyaccesseditems
//
// Removes the remaining IOMAD chrome blocks — the learner experience must contain
// ZERO company-admin chrome. Re-runs safely (upserts by blockname+region).
//
// Run: docker exec iomad-sandbox-webserver-1 php /var/www/html/theme/aurora/cli_seed_dashboard_blocks.php

define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/lib/blocklib.php');
require_once($CFG->dirroot . '/my/lib.php');

global $DB;

$pagetypepattern = 'my-index';
$subpagepattern  = '2';        // my_pages id of the default private dashboard.
$parentcontextid = \context_system::instance()->id;  // = 1.

// Desired layout: blockname => [region, weight].
// EVERYTHING rail-bound goes in side-pre: 9 of the 10 tenant themes declare only
// side-pre on the dashboard, and Moodle appends blocks from undeclared regions
// (side-post) to the END of the default region — which buried the learningpath
// block below the timeline. One region, explicit order, same result everywhere.
$layout = [
    // Middle column (content region, rendered inside main_content by my/index.php).
    // iomad_learningpath leads: it is the widest, richest card (expandable groups
    // and per-course progress) and needs the room. Its path.js binds global
    // selectors, so never create a second instance on this page.
    'iomad_learningpath'      => ['content',  0],
    'lp'                      => ['content',  1],
    'myoverview'              => ['content',  2],
    // Rail. learningpath (SSS learning paths block) sits at the top so the
    // learner sees their paths and current position first thing.
    'learningpath'            => ['side-pre', 0],
    'timeline'                => ['side-pre', 1],
    'calendar_month'          => ['side-pre', 2],
    'recentlyaccessedcourses' => ['side-pre', 3],
    'badges'                  => ['side-pre', 4],
    'completion_progress'     => ['side-pre', 5],
    'recentlyaccesseditems'   => ['side-pre', 6],
];

// Blocks that must NOT appear in the learner dashboard (IOMAD chrome / dupes).
$remove = ['iomad_company_admin', 'mycourses'];

$baserec = [
    'parentcontextid'     => $parentcontextid,
    'showinsubcontexts'   => 0,
    'pagetypepattern'     => $pagetypepattern,
    'subpagepattern'      => $subpagepattern,
];

echo "== Aurora dashboard block seeding (my-index, subpage {$subpagepattern}) ==\n";

// 1) Remove unwanted blocks on this page.
foreach ($remove as $bn) {
    $existing = $DB->get_records('block_instances', array_merge($baserec, ['blockname' => $bn]));
    foreach ($existing as $bi) {
        blocks_delete_instance($bi);
        echo "  removed  {$bn} (id={$bi->id})\n";
    }
}

// 2) Upsert each desired block into its region/weight.
foreach ($layout as $blockname => [$region, $weight]) {
    // Verify the block plugin exists.
    if (!core_component::get_plugin_directory('block', $blockname)) {
        echo "  SKIP     {$blockname} (plugin not installed)\n";
        continue;
    }

    $existing = $DB->get_records('block_instances', array_merge($baserec, ['blockname' => $blockname]));

    if ($existing) {
        // Keep the first, drop any duplicates, then move it to the right slot.
        $first = array_shift($existing);
        foreach ($existing as $dupe) {
            blocks_delete_instance($dupe);
            echo "  dedupe   {$blockname} (removed id={$dupe->id})\n";
        }
        $changed = ($first->defaultregion !== $region)
            || ((int) $first->defaultweight !== $weight)
            || ((int) $first->showinsubcontexts !== 0);
        $first->defaultregion    = $region;
        $first->defaultweight    = $weight;
        $first->showinsubcontexts = 0;
        // Clear any per-instance visibility override that could hide it.
        $DB->update_record('block_instances', $first);
        // Mirror region/weight into block_positions so it lands where intended.
        $DB->delete_records('block_positions', ['blockinstanceid' => $first->id]);
        echo '  ' . ($changed ? 'moved   ' : 'kept    ') . " {$blockname} -> {$region} (w={$weight}, id={$first->id})\n";
        continue;
    }

    // Create a fresh instance.
    $record = (object) array_merge($baserec, [
        'blockname'        => $blockname,
        'defaultregion'    => $region,
        'defaultweight'    => $weight,
        'configdata'       => '',
        'timecreated'      => time(),
        'timemodified'     => time(),
    ]);
    $record->id = $DB->insert_record('block_instances', $record);
    // Give core a chance to record the block context.
    \context_block::instance($record->id);
    echo "  created  {$blockname} -> {$region} (w={$weight}, id={$record->id})\n";
}

// 3) Propagate the new default layout to every user's private dashboard.
my_reset_page_for_all_users(MY_PAGE_PRIVATE, 'my-index');
echo "  propagated to all users (my_reset_page_for_all_users)\n";

// 3b) Boost-family themes collapse the block drawer for fresh accounts and the
// space themes have their own sidebar toggle. Open both for every active user
// so the rail is visible without a click.
$users = $DB->get_records_select('user', 'deleted = 0 AND suspended = 0 AND id > 1', [], 'id', 'id');
foreach ($users as $u) {
    set_user_preference('drawer-open-block', 'true', $u->id);
    set_user_preference('drawer-open-nav', 'true', $u->id);
}
echo '  drawer prefs opened for ' . count($users) . " users\n";

// 4) Show the resulting layout.
echo "\n== Resulting layout ==\n";
$rows = $DB->get_records('block_instances', $baserec, 'defaultregion, defaultweight');
foreach ($rows as $r) {
    echo "  {$r->defaultregion}\t{$r->blockname}\t(w={$r->defaultweight}, id={$r->id})\n";
}

purge_all_caches();
echo "\nDone.\n";
