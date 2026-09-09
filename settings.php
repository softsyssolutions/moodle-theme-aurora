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
 * Aurora theme settings. Replicates the boost settings tree (so the admin can
 * configure the theme - settings.php is NOT inherited) plus the Aurora chrome
 * settings (greeting, streak target, weekly goal, recommended copy).
 *
 * @package    theme_aurora
 * @copyright  2026 SoftSys Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingaurora', get_string('configtitle', 'theme_aurora'));

    // ------------------------------------------------------------------
    // Tab 1: General (boost parity).
    // ------------------------------------------------------------------
    $page = new admin_settingpage('theme_aurora_general', get_string('generalsettings', 'theme_aurora'));

    // Unaddable blocks (boost parity).
    $default = 'navigation,settings,course_list,section_links';
    $setting = new admin_setting_configtext('theme_aurora/unaddableblocks',
        get_string('unaddableblocks', 'theme_aurora'), get_string('unaddableblocks_desc', 'theme_aurora'),
        $default, PARAM_TEXT);
    $page->add($setting);

    // Preset.
    $name = 'theme_aurora/preset';
    $title = get_string('preset', 'theme_aurora');
    $description = get_string('preset_desc', 'theme_aurora');
    $default = 'default.scss';
    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_aurora', 'preset', 0, 'itemid, filepath, filename', false);
    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';
    $setting = new admin_setting_configthemepreset($name, $title, $description, $default, $choices, 'boost');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files.
    $name = 'theme_aurora/presetfiles';
    $title = get_string('presetfiles', 'theme_aurora');
    $description = get_string('presetfiles_desc', 'theme_aurora');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        ['maxfiles' => 20, 'accepted_types' => ['.scss']]);
    $page->add($setting);

    // Brand color (board green).
    $name = 'theme_aurora/brandcolor';
    $title = get_string('brandcolor', 'theme_aurora');
    $description = get_string('brandcolor_desc', 'theme_aurora');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#188838');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Accent color (sticky yellow).
    $name = 'theme_aurora/accentcolor';
    $title = get_string('accentcolor', 'theme_aurora');
    $description = get_string('accentcolor_desc', 'theme_aurora');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#f8db53');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Secondary color (sticky green).
    $name = 'theme_aurora/secondcolor';
    $title = get_string('secondcolor', 'theme_aurora');
    $description = get_string('secondcolor_desc', 'theme_aurora');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#a2d984');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Logo.
    $name = 'theme_aurora/logo';
    $title = get_string('logo', 'theme_aurora');
    $description = get_string('logo_desc', 'theme_aurora');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.svg', '.gif', '.webp']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Background image.
    $name = 'theme_aurora/backgroundimage';
    $title = get_string('backgroundimage', 'theme_aurora');
    $description = get_string('backgroundimage_desc', 'theme_aurora');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Login background image.
    $name = 'theme_aurora/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_aurora');
    $description = get_string('loginbackgroundimage_desc', 'theme_aurora');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // ------------------------------------------------------------------
    // Tab 2: Aurora chrome (gamification / greeting copy).
    // ------------------------------------------------------------------
    $page = new admin_settingpage('theme_aurora_chrome', get_string('chromesettings', 'theme_aurora'));

    // Greeting line shown in the topbar / hero (the firstname is appended live).
    $name = 'theme_aurora/greeting';
    $title = get_string('greeting', 'theme_aurora');
    $description = get_string('greeting_desc', 'theme_aurora');
    $setting = new admin_setting_configtext($name, $title, $description,
        get_string('default_greeting', 'theme_aurora'), PARAM_TEXT);
    $page->add($setting);

    // Show the sidebar streak / learning-streak card.
    $name = 'theme_aurora/showstreak';
    $title = get_string('showstreak', 'theme_aurora');
    $description = get_string('showstreak_desc', 'theme_aurora');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $page->add($setting);

    // Streak target (days to next achievement) - drives the 7 segment bar.
    $name = 'theme_aurora/streaktarget';
    $title = get_string('streaktarget', 'theme_aurora');
    $description = get_string('streaktarget_desc', 'theme_aurora');
    $setting = new admin_setting_configtext($name, $title, $description, '7', PARAM_INT);
    $page->add($setting);

    // Current streak count (demo value) - sidebar card value + topbar pill.
    $name = 'theme_aurora/streakcount';
    $title = get_string('streakcount', 'theme_aurora');
    $description = get_string('streakcount_desc', 'theme_aurora');
    $setting = new admin_setting_configtext($name, $title, $description, '0', PARAM_INT);
    $page->add($setting);

    // Panel subtitle (under the "My panel" heading on the dashboard).
    // The dashboard body itself is a REAL 3-column block layout (side-pre /
    // content / side-post) — admins place blocks there; no theme settings drive
    // its data. This subtitle is the only dashboard chrome that remains.
    $name = 'theme_aurora/panelsubtitle';
    $title = get_string('panelsubtitle', 'theme_aurora');
    $description = get_string('panelsubtitle_desc', 'theme_aurora');
    $setting = new admin_setting_configtext($name, $title, $description,
        get_string('default_panelsubtitle', 'theme_aurora'), PARAM_TEXT);
    $page->add($setting);

    // Recommended block heading copy (Inicio "Recomendados para vos").
    $name = 'theme_aurora/recommendedheading';
    $title = get_string('recommendedheading', 'theme_aurora');
    $description = get_string('recommendedheading_desc', 'theme_aurora');
    $setting = new admin_setting_configtext($name, $title, $description,
        get_string('default_recommendedheading', 'theme_aurora'), PARAM_TEXT);
    $page->add($setting);

    // Recommended block subheading copy.
    $name = 'theme_aurora/recommendedsubheading';
    $title = get_string('recommendedsubheading', 'theme_aurora');
    $description = get_string('recommendedsubheading_desc', 'theme_aurora');
    $setting = new admin_setting_configtext($name, $title, $description,
        get_string('default_recommendedsubheading', 'theme_aurora'), PARAM_TEXT);
    $page->add($setting);

    // Home stat-row: in-progress count.
    $setting = new admin_setting_configtext('theme_aurora/statsinprogress',
        get_string('statsinprogress', 'theme_aurora'), get_string('statsinprogress_desc', 'theme_aurora'),
        '3', PARAM_INT);
    $page->add($setting);

    // Home stat-row: completed count.
    $setting = new admin_setting_configtext('theme_aurora/statscompleted',
        get_string('statscompleted', 'theme_aurora'), get_string('statscompleted_desc', 'theme_aurora'),
        '12', PARAM_INT);
    $page->add($setting);

    // Home stat-row: awards count.
    $setting = new admin_setting_configtext('theme_aurora/statsawards',
        get_string('statsawards', 'theme_aurora'), get_string('statsawards_desc', 'theme_aurora'),
        '5', PARAM_INT);
    $page->add($setting);

    // Home stat-row: learning hours.
    $setting = new admin_setting_configtext('theme_aurora/statshours',
        get_string('statshours', 'theme_aurora'), get_string('statshours_desc', 'theme_aurora'),
        '48', PARAM_INT);
    $page->add($setting);

    $settings->add($page);

    // ------------------------------------------------------------------
    // Tab 3: Advanced (raw SCSS - boost parity).
    // ------------------------------------------------------------------
    $page = new admin_settingpage('theme_aurora_advanced', get_string('advancedsettings', 'theme_aurora'));

    $setting = new admin_setting_scsscode('theme_aurora/scsspre',
        get_string('rawscsspre', 'theme_aurora'), get_string('rawscsspre_desc', 'theme_aurora'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_scsscode('theme_aurora/scss',
        get_string('rawscss', 'theme_aurora'), get_string('rawscss_desc', 'theme_aurora'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
