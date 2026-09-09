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
 * Aurora theme upgrade steps.
 *
 * @package    theme_aurora
 * @copyright  2026 SoftSys Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Aurora theme upgrade function.
 *
 * @param int $oldversion Previous installed version.
 * @return bool
 */
function xmldb_theme_aurora_upgrade($oldversion) {

    if ($oldversion < 2026090819) {
        // Migrate legacy SaaS colour defaults to Aurora Whiteboard identity.
        //
        // Only migrate when the stored value exactly matches the old shipped
        // default — a site that intentionally customised the colour is left
        // untouched; a brand-new install (no stored value) is also skipped
        // because lib.php::theme_aurora_get_pre_scss() already uses the new
        // defaults when the config key is absent or empty.

        $migrations = [
            'brandcolor'  => ['legacy' => '#6d5df6', 'whiteboard' => '#188838'],
            'accentcolor' => ['legacy' => '#ff6f5e', 'whiteboard' => '#f8db53'],
            'secondcolor' => ['legacy' => '#a78bfa', 'whiteboard' => '#a2d984'],
        ];

        foreach ($migrations as $key => $map) {
            $current = get_config('theme_aurora', $key);
            // Only migrate the exact legacy default; any other value is intentional.
            if ($current === $map['legacy']) {
                set_config($key, $map['whiteboard'], 'theme_aurora');
            }
        }

        // Reset theme caches so the new colours compile immediately.
        theme_reset_all_caches();

        upgrade_plugin_savepoint(true, 2026090819, 'theme', 'aurora');
    }

    return true;
}
