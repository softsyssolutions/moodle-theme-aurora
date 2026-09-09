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
 * Lucide icon system for theme_aurora.
 *
 * Mirrors the PHP icon_system_lucide: mapped icons render as inline Lucide
 * SVGs, unmapped icons fall back to the standard pix <img> URL.
 *
 * @module     theme_aurora/icon_system_lucide
 * @copyright  2026 Industria Elearning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import IconSystem from 'core/icon_system';
import * as Mustache from 'core/mustache';
import * as CoreUrl from 'core/url';
import LucideMap from 'theme_aurora/lucide_map';

/**
 * The Lucide icon system.
 */
export default class IconSystemLucide extends IconSystem {

    /**
     * Render an icon.
     *
     * @param {string} key
     * @param {string} component
     * @param {string} title
     * @param {string} template
     * @return {string}
     */
    renderIcon(key, component, title, template) {
        const mappedComponent = (typeof component === 'undefined' || component === 'moodle' || component === '')
            ? 'core'
            : component;
        const mapKey = `${mappedComponent}:${key}`;
        const alt = (typeof title === 'undefined') ? '' : title;

        if (Object.prototype.hasOwnProperty.call(LucideMap, mapKey)) {
            const [svg, classes] = LucideMap[mapKey];
            const context = {
                unmappedIcon: false,
                svg: svg,
                title: alt,
                alt: alt,
                extraclasses: classes,
            };
            if (alt === '') {
                context['aria-hidden'] = 'true';
            }
            return Mustache.render(template, context).trim();
        }

        // Unmapped: standard pix <img> fallback.
        const url = CoreUrl.imageUrl(key, component);
        const attributes = [
            {name: 'src', value: url},
            {name: 'alt', value: alt},
            {name: 'title', value: alt},
        ];
        if (alt === '') {
            attributes.push({name: 'aria-hidden', value: 'true'});
        }
        return Mustache.render(template, {
            unmappedIcon: true,
            attributes: attributes,
            extraclasses: '',
        }).trim();
    }

    /**
     * Get the name of the template to pre-cache for this icon system.
     *
     * @return {string}
     */
    getTemplateName() {
        return 'theme_aurora/pix_icon_lucide';
    }
}
