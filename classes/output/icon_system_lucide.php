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

namespace theme_aurora\output;

use core\output\icon_system;
use core\output\pix_icon;
use core\output\renderer_base;

/**
 * Lucide icon system for theme_aurora.
 *
 * Renders mapped pix icons as inline Lucide SVGs (stroke="currentColor",
 * so icons inherit text colour). Unmapped icons fall back to the standard
 * <img> pix rendering, so nothing disappears for plugins we have not mapped.
 *
 * @package    theme_aurora
 * @copyright  2026 Industria Elearning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class icon_system_lucide extends icon_system {

    /** @var array|null Cached lucide map (moodle key => ['svg' => ..., 'classes' => ...]). */
    private static $lucidmap = null;

    /**
     * Load the generated lucide map.
     *
     * @return array
     */
    private function get_lucide_map(): array {
        if (self::$lucidmap === null) {
            self::$lucidmap = include(__DIR__ . '/lucide_map.php');
        }
        return self::$lucidmap;
    }

    #[\Override]
    public function get_amd_name() {
        return 'theme_aurora/icon_system_lucide';
    }

    #[\Override]
    public function render_pix_icon(renderer_base $output, pix_icon $icon) {
        $component = $icon->component;
        if (empty($component) || $component === 'moodle') {
            $component = 'core';
        } else if ($component !== 'theme') {
            $component = \core_component::normalize_componentname($component);
        }
        $key = $component . ':' . $icon->pix;
        $map = $this->get_lucide_map();

        $alt = $icon->attributes['alt'] ?? '';
        $title = $icon->attributes['title'] ?? $alt;
        $extraclasses = $icon->attributes['class'] ?? '';

        if (isset($map[$key])) {
            $data = [
                'unmappedIcon' => false,
                'svg' => $map[$key]['svg'],
                'title' => $title,
                'alt' => $alt,
                'extraclasses' => trim($extraclasses . ' ' . $map[$key]['classes']),
            ];
            if (isset($icon->attributes['aria-hidden'])) {
                $data['aria-hidden'] = $icon->attributes['aria-hidden'];
            } else if ($alt === '') {
                $data['aria-hidden'] = 'true';
            }
            return $output->render_from_template('theme_aurora/pix_icon_lucide', $data);
        }

        // Unmapped: fall back to the standard pix <img> rendering.
        $url = $output->image_url($icon->pix, $icon->component);
        $attributes = [
            ['name' => 'src', 'value' => $url->out(false)],
            ['name' => 'alt', 'value' => $alt],
            ['name' => 'title', 'value' => $title],
        ];
        if ($alt === '') {
            $attributes[] = ['name' => 'aria-hidden', 'value' => 'true'];
        }
        $data = [
            'unmappedIcon' => true,
            'attributes' => $attributes,
            'extraclasses' => $extraclasses,
        ];
        return $output->render_from_template('theme_aurora/pix_icon_lucide', $data);
    }
}
