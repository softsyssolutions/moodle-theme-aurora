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
 * Aurora theme library functions.
 *
 * A thin child of boost: reuses boost's preset SCSS, then appends Aurora's
 * own post.scss. Replicates every boost pluginfile branch so logo / background
 * / login background are served for the CHILD theme (Moodle calls
 * theme_aurora_pluginfile, never the parent's).
 *
 * @package    theme_aurora
 * @copyright  2026 SoftSys Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the main SCSS content: boost preset chain + Aurora post.scss.
 *
 * We reuse theme_boost_get_main_scss_content() so the full Bootstrap/Boost
 * preset (~1.6MB compiled) is inherited; overriding $THEME->scss without this
 * reuse would yield a tiny unstyled stylesheet.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_aurora_get_main_scss_content($theme) {
    global $CFG;

    require_once($CFG->dirroot . '/theme/boost/lib.php');

    // Inherit the boost preset using the CHILD theme settings (so a preset
    // uploaded to theme_aurora is respected); fall back to boost.
    $boosttheme = theme_config::load('boost');
    $scss = theme_boost_get_main_scss_content($theme->settings->preset ? $theme : $boosttheme);

    // Append Aurora SCSS last so it wins over the boost preset.
    //
    // We concatenate the partials directly via file_get_contents() instead of
    // relying on ScssPhp @import resolution inside main.scss: core_scss does
    // not set an import path for the theme scss/ dir on this Moodle build, so
    // every @import in main.scss (components/tokens, post-legacy, …) silently
    // fails to resolve and the compiled CSS ships boost-only. The order below
    // mirrors main.scss: tokens → base → buttons → progress → post-legacy
    // (post-legacy is the canonical full skin).
    $scssdir = $CFG->dirroot . '/theme/aurora/scss';
    $partials = [
        'components/_tokens.scss',
        'components/_base.scss',
        'components/_buttons.scss',
        'components/_progress.scss',
        'components/_icons.scss',
        'post-legacy.scss',
        // Page-level skins appended AFTER post-legacy so they win.
        'components/_calendar-page.scss',
        // Identity primitives: erase legacy purple from all learner surfaces.
        'components/_ui-primitives.scss',
        // Course & in-course skin: green active states, paper sections.
        'components/_course-incourse.scss',
        // Frontpage landing sections: catalogue, steps, value, CTA bar.
        'components/_frontpage-sections.scss',
    ];
    foreach ($partials as $partial) {
        $path = $scssdir . '/' . $partial;
        if (is_readable($path)) {
            $scss .= "\n" . file_get_contents($path);
        }
    }

    return $scss;
}

/**
 * Pre-SCSS: forward Aurora settings onto Bootstrap/Boost variables BEFORE the
 * preset compiles, so the board green, accent yellow and secondary green take hold.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_aurora_get_pre_scss($theme) {
    $scss = '';

    // Map Aurora settings -> SCSS variables (with Aurora Whiteboard defaults).
    $configurable = [
        'brandcolor'   => ['primary'],
        'accentcolor'  => ['aurora-accent'],
        'secondcolor'  => ['aurora-lilac'],
    ];
    $defaults = [
        'brandcolor'  => '#188838',
        'accentcolor' => '#f8db53',
        'secondcolor' => '#a2d984',
    ];

    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) && $theme->settings->{$configkey} !== ''
            ? $theme->settings->{$configkey}
            : $defaults[$configkey];
        foreach ($targets as $target) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }
    }

    // Prepend admin pre-scss last.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 * Extra SCSS: background image, login background image and admin raw SCSS.
 * Replicates the boost branches but reads from the Aurora settings.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_aurora_get_extra_scss($theme) {
    $content = '';

    $imageurl = $theme->setting_file_url('backgroundimage', 'backgroundimage');
    if (!empty($imageurl)) {
        $content .= '@media (min-width: 768px) {';
        $content .= 'body { ';
        $content .= "background-image: url('$imageurl'); background-size: cover;";
        $content .= ' } }';
    }

    $loginbg = $theme->setting_file_url('loginbackgroundimage', 'loginbackgroundimage');
    if (!empty($loginbg)) {
        $content .= 'body.pagelayout-login #page { ';
        $content .= "background-image: url('$loginbg'); background-size: cover;";
        $content .= ' }';
    }

    return !empty($theme->settings->scss) ? "{$theme->settings->scss}  \n  {$content}" : $content;
}

/**
 * User preferences carried over from boost (drawer states).
 *
 * @return array[]
 */
function theme_aurora_user_preferences(): array {
    return [
        'drawer-open-block' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'drawer-open-index' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => true,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
    ];
}

/**
 * Real course image URL from course settings (overviewfiles).
 *
 * Prefers Moodle's course_image cache — the same source as the course
 * edit form — then falls back to overviewfiles. Never invents a pattern.
 *
 * @param stdClass|\core_course_list_element $course
 * @return string
 */
function theme_aurora_course_image_url($course): string {
    if (empty($course) || empty($course->id)) {
        return '';
    }

    // Build from overviewfiles first so the host matches $CFG->wwwroot.
    // The course_image cache in this demo still stores http://localhost/...
    try {
        $listelement = $course instanceof \core_course_list_element
            ? $course
            : new \core_course_list_element($course);
        foreach ($listelement->get_course_overviewfiles() as $file) {
            if (strpos($file->get_mimetype(), 'image') !== false) {
                return \moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    null,
                    $file->get_filepath(),
                    $file->get_filename()
                )->out(false);
            }
        }
    } catch (\Throwable $e) {
        // Fall through to the Moodle cache.
    }

    if (class_exists(\core_course\external\course_summary_exporter::class)) {
        $cached = \core_course\external\course_summary_exporter::get_course_image($course);
        if (!empty($cached) && preg_match('#(/pluginfile\.php/.*)$#', (string) $cached, $match)) {
            return (new \moodle_url($match[1]))->out(false);
        }
        if (!empty($cached)) {
            return (string) $cached;
        }
    }
    return '';
}

/**
 * Serves theme settings files (logo, background, login background).
 *
 * Replicates every boost branch but loads the CHILD theme config, so the files
 * resolve against theme_aurora and do not 404.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_aurora_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel == CONTEXT_SYSTEM && (
        $filearea === 'logo' ||
        $filearea === 'backgroundimage' ||
        $filearea === 'loginbackgroundimage'
    )) {
        $theme = theme_config::load('aurora');
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}
