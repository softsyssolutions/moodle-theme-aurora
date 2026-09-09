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
 * Full-width CSS-Grid layout for the Aurora theme.
 *
 * This is a near-exact copy of theme_boost/layout/drawers.php that:
 *  - keeps EVERY piece of Boost machinery intact (navbar, course-index drawer,
 *    block drawer, drawer togglers, secondary navigation, activity header,
 *    footer, RequireJS bootstrap, all data-* attributes),
 *  - collects a SECOND block region (side-post) which is rendered INLINE as a
 *    right rail inside #page-content (the 320px column), while side-pre remains
 *    in Boost's native off-canvas block drawer,
 *  - tags <body> with the `layout-fullwidth` class so post.scss can strip the
 *    830px max-width cap and turn #page-content into a CSS Grid.
 *
 * IMPORTANT: "content" is NOT a Moodle block region. The center column is the
 * real page content emitted by output.main_content(). Only side-pre and
 * side-post are block regions.
 *
 * @package   theme_aurora
 * @copyright 2026 SoftSys Solutions
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode (shared by both regions).
$addblockbutton = $OUTPUT->addblockbutton();

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING') && get_user_preferences('behat_keep_drawer_closed') != 1) {
    $blockdraweropen = true;
}

// Body classes: keep Boost's uses-drawers (drawer JS + course-index), add our
// fullwidth marker. The extra class is what post.scss keys off.
$extraclasses = ['uses-drawers', 'layout-fullwidth'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

// side-pre: Boost's native right block drawer (off-canvas).
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}

// side-post: the INLINE right rail rendered inside the content grid (the 320px
// column from the mockup). This is a genuine second block region; admins place
// "Continúa", "Avisos", "Enlaces rápidos" blocks here via the block picker.
$sidepostblocks = $OUTPUT->blocks('side-post');
$hassidepost = (strpos($sidepostblocks, 'data-block=') !== false || !empty($addblockbutton));

$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'sidepostblocks' => $sidepostblocks,
    'hasblocks' => $hasblocks,
    'hassidepost' => $hassidepost,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
];

echo $OUTPUT->render_from_template('theme_aurora/fullwidth', $templatecontext);
