<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Apply Aurora Whiteboard badge + course raster assets from pix/whiteboard/.
 *
 * Reads DESIGN.md Asset generation conventions. Safe to re-run.
 *
 * Usage (from Moodle root inside the container):
 *   php theme/aurora/cli/seed_whiteboard_assets.php
 *
 * @package    theme_aurora
 * @copyright  2026 SoftSys Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/course/lib.php');

[$options, $unrecognized] = cli_get_params([
    'help' => false,
    'dry-run' => false,
], [
    'h' => 'help',
]);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if (!empty($options['help'])) {
    echo "Apply theme_aurora whiteboard badge/course images from pix/whiteboard/\n\n";
    echo "Options:\n";
    echo "  --dry-run   Show mappings without writing\n";
    echo "  -h, --help  This help\n";
    exit(0);
}

$dryrun = !empty($options['dry-run']);
$assetdir = $CFG->dirroot . '/theme/aurora/pix/whiteboard';

if (!is_dir($assetdir)) {
    cli_error('Missing asset directory: ' . $assetdir);
}

/** @var array<string,string> badge name => basename without extension */
$badgemap = [
    'Productividad Pro' => 'badge-productividad-pro',
    'Finanzas Saludables' => 'badge-finanzas-saludables',
    'Excel Master' => 'badge-excel-master',
    'Marketing Starter' => 'badge-marketing-starter',
    'Comunicador Efectivo' => 'badge-comunicador-efectivo',
];

/** @var array<string,string> course shortname => basename without extension */
$coursemap = [
    'aprende-c1' => 'course-aprende-c1',
    'aprende-c2' => 'course-aprende-c2',
    'aprende-c3' => 'course-aprende-c3',
    'aprende-c4' => 'course-aprende-c4',
    'aprende-c5' => 'course-aprende-c5',
    'curso-ley-de-proteccion-de-datos-personales-msdb4896' => 'course-aprende-c6',
    'LP-APRENDE-ONB1' => 'course-LP-APRENDE-ONB1',
    'LP-APRENDE-ONB2' => 'course-LP-APRENDE-ONB2',
];

$fs = get_file_storage();
$applied = 0;
$skipped = 0;

cli_heading('Aurora whiteboard assets');

foreach ($badgemap as $name => $basename) {
    $path = $assetdir . '/' . $basename . '.png';
    if (!is_readable($path)) {
        cli_problem("SKIP badge missing file: {$basename}.png");
        $skipped++;
        continue;
    }
    $badge = $DB->get_record('badge', ['name' => $name]);
    if (!$badge) {
        cli_problem("SKIP badge not in DB: {$name}");
        $skipped++;
        continue;
    }
    cli_writeln(($dryrun ? '[dry] ' : '') . "badge #{$badge->id} «{$name}» ← {$basename}.png");
    if ($dryrun) {
        continue;
    }
    $badgeobj = new badge($badge->id);
    // badges_process_badge_image deletes the temp path after processing.
    $tmpdir = make_request_directory();
    $tmp = $tmpdir . '/' . $basename . '.png';
    if (!copy($path, $tmp)) {
        cli_problem("Failed to copy {$path}");
        $skipped++;
        continue;
    }
    badges_process_badge_image($badgeobj, $tmp);
    $applied++;
}

foreach ($coursemap as $shortname => $basename) {
    $jpg = $assetdir . '/' . $basename . '.jpg';
    $png = $assetdir . '/' . $basename . '.png';
    $path = is_readable($jpg) ? $jpg : (is_readable($png) ? $png : null);
    if ($path === null) {
        cli_problem("SKIP course missing file: {$basename}.jpg|.png");
        $skipped++;
        continue;
    }
    $course = $DB->get_record('course', ['shortname' => $shortname]);
    if (!$course) {
        cli_problem("SKIP course not in DB: {$shortname}");
        $skipped++;
        continue;
    }
    $filename = basename($path);
    cli_writeln(($dryrun ? '[dry] ' : '') . "course #{$course->id} «{$shortname}» ← {$filename}");
    if ($dryrun) {
        continue;
    }
    $context = context_course::instance($course->id);
    $fs->delete_area_files($context->id, 'course', 'overviewfiles');
    $fs->create_file_from_pathname([
        'contextid' => $context->id,
        'component' => 'course',
        'filearea' => 'overviewfiles',
        'itemid' => 0,
        'filepath' => '/',
        'filename' => $filename,
    ], $path);
    // Bust course image caches.
    $course->cacherev = time();
    $DB->update_record('course', $course);
    $applied++;
}

purge_all_caches();
cli_writeln("Done. applied={$applied} skipped={$skipped}" . ($dryrun ? ' (dry-run)' : ''));
exit(0);
