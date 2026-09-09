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
 * Aurora is a PURE skin over Boost: it ships NO custom layout files and NO
 * shell templates, so every page uses Boost's own layout/drawers.php (resolved
 * through $THEME->parents) and renders Boost's native navbar, course-index
 * drawer and block drawers untouched. All Aurora identity (colors, fonts,
 * cards, block restyles) lives in scss/post.scss, scoped to the `aurora-shell`
 * body class that core_renderer::body_attributes() adds. The only PHP overrides
 * are the favicon, the web-font <link> and that body class. No page data is
 * ever faked.
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

// Layouts mirror theme_boost EXACTLY. Aurora ships no layout/drawers.php, so
// 'file' => 'drawers.php' resolves to Boost's own layout/drawers.php via
// $THEME->parents — native navbar, course-index drawer and block drawers
// render unchanged. Bare/system layouts (login, popup, embedded, maintenance,
// secure, print, frametop, redirect) likewise reuse Boost's layout files.
$THEME->layouts = [
    // Most backwards compatible layout without the blocks.
    'base' => [
        'file' => 'drawers.php',
        'regions' => [],
    ],
    // Standard layout with blocks.
    'standard' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // Main course page.
    'course' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['langmenu' => true],
    ],
    'coursecategory' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // Part of course, typical for modules (lessons / mod pages).
    'incourse' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // The site home page (Inicio).
    'frontpage' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true],
    ],
    // Server administration scripts.
    'admin' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // My courses page (Mis cursos).
    'mycourses' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true],
    ],
    // My dashboard page (Mi panel) — native Boost drawers layout. Blocks live in
    // the standard side-pre block drawer; Aurora only reskins the real blocks via
    // post.scss. No bespoke region layout (that broke the native course-index /
    // block drawers); a richer dashboard is layered later as a proper Moodle
    // layout that does not touch native navigation.
    'mydashboard' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true, 'langmenu' => true],
    ],
    // My public page (profile).
    'mypublic' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
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
    // Reports.
    'report' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
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
$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;
$THEME->haseditswitch = true;
$THEME->usescourseindex = true;
// Aurora pages do not need their activity titles displayed (banner shows it).
$THEME->activityheaderconfig = [
    'notitle' => true,
];
