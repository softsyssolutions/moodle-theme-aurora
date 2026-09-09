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
 * Aurora theme config. Thin child of boost.
 *
 * Aurora is mostly a skin over Boost: pages render through Aurora's own
 * layout/fullwidth.php — a near-exact copy of Boost's layout/drawers.php that
 * keeps the native navbar, course-index drawer and block drawers untouched.
 * All Aurora identity (colors, fonts, cards, block restyles) lives in
 * scss/post.scss, scoped to the `aurora-shell` body class that
 * core_renderer::body_attributes() adds. The only PHP overrides are the
 * favicon, the web-font <link> and that body class. No page data is ever
 * faked.
 *
 * layout/fullwidth.php (+ templates/fullwidth.mustache) is used by every
 * drawers-based page type (standard, course, coursecategory, incourse,
 * frontpage, admin, mycourses, mydashboard, mypublic, report, fullwidth). It
 * adds a `layout-fullwidth` body class (so post.scss strips the 830px
 * max-width cap and turns #page-content into a CSS Grid) plus a second block
 * region (side-post) rendered as an inline 320px right rail. All Boost drawer
 * machinery (course-index drawer, block drawer, togglers, JS) is preserved.
 *
 * @package    theme_aurora
 * @copyright  2026 SoftSys Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

$THEME->name = 'aurora';
$THEME->parents = ['boost'];
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->editor_scss = ['editor'];
$THEME->usefallback = true;

$THEME->scss = function($theme) {
    return theme_aurora_get_main_scss_content($theme);
};

// SCSS callbacks. Pre forwards brandcolor/accent onto boost vars; extra handles
// the login background image + admin raw scss (replicating boost's branches).
// NOTE: no precompiledcsscallback - that would serve boost's static moodle.css
// and skip compiling Aurora's post.scss (the sidebar/topbar would never appear).
$THEME->prescsscallback = 'theme_aurora_get_pre_scss';
$THEME->extrascsscallback = 'theme_aurora_get_extra_scss';

// Layouts: every drawers-based page (standard, course, incourse, mycourses,
// mypublic, report, admin…) uses Aurora's layout/fullwidth.php — a near-exact
// copy of Boost drawers that removes the 830px content cap (CSS Grid) and adds
// an optional inline side-post rail. All Boost drawer machinery (course-index
// drawer, block drawer, togglers, JS) is preserved. Bare/system layouts
// (login, popup, embedded, maintenance, secure, print, frametop, redirect,
// base) keep Boost's own layout files, resolved via $THEME->parents.
$THEME->layouts = [
    // Most backwards compatible layout without the blocks (Boost drawers).
    'base' => [
        'file' => 'drawers.php',
        'regions' => [],
    ],
    // Standard layout with blocks — full-width (calendar, badges, blog…).
    'standard' => [
        'file' => 'fullwidth.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
    ],
    // Main course page — full-width.
    'course' => [
        'file' => 'fullwidth.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
        'options' => ['langmenu' => true],
    ],
    'coursecategory' => [
        'file' => 'fullwidth.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
    ],
    // Part of course, typical for modules (lessons / mod pages) — full-width.
    'incourse' => [
        'file' => 'fullwidth.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
    ],
    // The site home page (Inicio) — FULL-WIDTH grid: 100% viewport, inline
    // side-post rail (320px) + native Boost drawers (course-index left, block
    // drawer right). side-pre stays in the Boost block drawer; side-post is the
    // inline rail rendered inside the content grid.
    'frontpage' => [
        'file' => 'fullwidth.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true],
    ],
    // Server administration scripts — full-width.
    'admin' => [
        'file' => 'fullwidth.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
    ],
    // My courses page (Mis cursos) — full-width.
    'mycourses' => [
        'file' => 'fullwidth.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true],
    ],
    // My dashboard page (Mi panel) — FULL-WIDTH grid (same as frontpage).
    // side-pre blocks live in the Boost block drawer; side-post blocks render in
    // the inline 320px right rail. The real dashboard widgets (myoverview /
    // timeline / calendar) emit through output.main_content() in the center
    // column — nothing is faked or hidden.
    'mydashboard' => [
        'file' => 'fullwidth.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true, 'langmenu' => true],
    ],
    // Standalone full-width layout any page can opt into (e.g. via a local
    // plugin that calls $PAGE->set_pagelayout('fullwidth')). Same machinery as
    // frontpage/mydashboard above.
    'fullwidth' => [
        'file' => 'fullwidth.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
        'options' => ['langmenu' => true],
    ],
    // My public page (profile) — full-width.
    'mypublic' => [
        'file' => 'fullwidth.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
    ],
    // Login page (reuses Boost's login layout + template).
    'login' => [
        'file' => 'login.php',
        'regions' => [],
        'options' => ['langmenu' => true],
    ],
    // Pages in pop-up windows - no navigation, no blocks, no header.
    'popup' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => [
            'nofooter' => true,
            'nonavbar' => true,
            'activityheader' => [
                'notitle' => true,
                'nocompletion' => true,
                'nodescription' => true,
            ],
        ],
    ],
    // No blocks and minimal footer - legacy frame layouts.
    'frametop' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => [
            'nofooter' => true,
            'nocoursefooter' => true,
            'activityheader' => [
                'nocompletion' => true,
            ],
        ],
    ],
    // Embedded pages (iframe/object) - as much space as possible.
    'embedded' => [
        'file' => 'embedded.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // Maintenance / install - must not touch DB or blocks.
    'maintenance' => [
        'file' => 'maintenance.php',
        'regions' => [],
    ],
    // Content and basic headers only.
    'print' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => false, 'noactivityheader' => true],
    ],
    // Redirection page.
    'redirect' => [
        'file' => 'embedded.php',
        'regions' => [],
    ],
    // Reports — full-width.
    'report' => [
        'file' => 'fullwidth.php',
        'regions' => ['side-pre', 'side-post'],
        'defaultregion' => 'side-pre',
    ],
    // Safebrowser / secure window.
    'secure' => [
        'file' => 'secure.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => [
            'activityheader' => [
                'notitle' => false,
            ],
        ],
    ],
];

$THEME->enable_dock = false;
$THEME->yuicssmodules = [];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
// Lucide inline-SVG icon system (monoline, stroke="currentColor") replaces
// FontAwesome glyphs. Unmapped icons fall back to standard pix images.
$THEME->iconsystem = '\\theme_aurora\\output\\icon_system_lucide';
$THEME->haseditswitch = true;
$THEME->usescourseindex = true;
// Aurora pages do not need their activity titles displayed (banner shows it).
$THEME->activityheaderconfig = [
    'notitle' => true,
];
