<?php
// Idempotent CLI: lay out the REAL Aurora 3-column dashboard block set on the
// default dashboard page (my_pages id=2, pagetypepattern 'my-index',
// subpagepattern '2', parentcontextid 1 = system context).
//
//   side-pre  (left)  : timeline, calendar_month, recentlyaccessedcourses
//   content   (middle): myoverview  (sole block — owns the full 1fr wide column)
//   side-post (right) : badges, completion_progress, recentlyaccesseditems
//
// Removes the IOMAD chrome block (iomad_learningpath) — the learner experience
// must contain ZERO IOMAD chrome. Re-runs safely (upserts by blockname+region).
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
// Middle column = myoverview ONLY (full 1fr width). recentlyaccessedcourses
// moves to side-pre (left rail, below calendar) — it was cramped competing
// for width in the middle column and is thematically a quick-access card.
$layout = [
    // Left rail.
    'timeline'                => ['side-pre',  0],
    'calendar_month'          => ['side-pre',  1],
    'recentlyaccessedcourses' => ['side-pre',  2],
    // Middle column (content region, rendered inside main_content by my/index.php).
    'myoverview'              => ['content',   0],
    // Right rail.
    'badges'                  => ['side-post', 0],
    'completion_progress'     => ['side-post', 1],
    'recentlyaccesseditems'   => ['side-post', 2],
];

// Blocks that must NOT appear in the learner dashboard (IOMAD chrome / dupes).
$remove = ['iomad_learningpath', 'iomad_company_admin', 'mycourses'];

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

// 4) Show the resulting layout.
echo "\n== Resulting layout ==\n";
$rows = $DB->get_records('block_instances', $baserec, 'defaultregion, defaultweight');
foreach ($rows as $r) {
    echo "  {$r->defaultregion}\t{$r->blockname}\t(w={$r->defaultweight}, id={$r->id})\n";
}

purge_all_caches();
echo "\nDone.\n";
