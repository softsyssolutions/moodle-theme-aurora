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

namespace theme_aurora\output\core;

use core_course_category;
use core_course_list_element;
use context_course;
use context_system;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Aurora course renderer.
 *
 * Registered via theme_overridden_renderer_factory when the page calls
 *   $PAGE->get_renderer('core', 'course')
 * which resolves to theme_aurora\output\core\course_renderer
 * (file: classes/output/core/course_renderer.php).
 *
 * Overrides the frontpage course-listing methods to render a rich
 * Whiteboard-style catalogue using real Moodle course data — no fake
 * objects, no invented counts.
 *
 * Two overrides:
 *  - frontpage_available_courses() → rich visual catalogue + landing sections
 *    rendered via theme_aurora/frontpage_courses Mustache template.
 *  - frontpage_my_courses() → empty string; the frontpage is guest-first and
 *    /my/ + /my/courses.php own the enrolled-course experience.
 *
 * @package    theme_aurora
 * @copyright  2026 SoftSys Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_renderer extends \core_course_renderer {

    /**
     * Suppress the "my enrolled courses" list on the frontpage.
     *
     * The frontpage is guest-first; returning an empty string here ensures
     * logged-in users are not shown a duplicate of the /my/ board. The
     * dashboard (/my/) and /my/courses.php own that experience.
     *
     * @return string
     */
    public function frontpage_my_courses(): string {
        return '';
    }

    /**
     * Returns the rich Aurora visual catalogue for the frontpage.
     *
     * Uses the same core_course_category::top()->get_courses() query and the
     * same coursecat_helper options as the parent so visibility, permission
     * and frontpagecourselimit are all respected. Real course data only
     * (name, URL, summary, overview image). Falls back to the
     * standard "add new course" button when the creator has no courses yet.
     *
     * @return string
     */
    public function frontpage_available_courses(): string {
        global $CFG;

        // Mirror core's chelper config exactly so visibility/perms are respected.
        $chelper = new \coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)
            ->set_courses_display_options([
                'recursive'   => true,
                'limit'       => $CFG->frontpagecourselimit,
                'viewmoreurl' => new moodle_url('/course/index.php'),
                'viewmoretext' => new \lang_string('fulllistofcourses'),
            ]);
        $chelper->set_attributes(['class' => 'frontpage-course-list-all']);

        $options    = $chelper->get_courses_display_options();
        $courses    = core_course_category::top()->get_courses($options);
        $totalcount = core_course_category::top()->get_courses_count($options);

        // No courses + creator → preserve core "add new course" button.
        if (!$totalcount
                && !$this->page->user_is_editing()
                && has_capability('moodle/course:create', context_system::instance())) {
            return $this->add_new_course_button();
        }

        $ctx = $this->aurora_fp_courses_context($courses);
        return $this->render_from_template('theme_aurora/frontpage_courses', $ctx);
    }

    /**
     * Build the Mustache context for theme_aurora/frontpage_courses.
     *
     * All course data comes from the Moodle API — no invented values.
     *
     * @param iterable $courses core_course_list_element objects from get_courses().
     * @return array Mustache template context.
     */
    protected function aurora_fp_courses_context(iterable $courses): array {
        $loggedin   = isloggedin() && !isguestuser();
        $coursedata = [];

        // Minimal rotational variation so cards feel hand-placed on the board.
        $rotations = ['-2deg', '1.5deg', '-1deg', '0deg', '2deg', '-1.5deg'];
        $idx = 0;

        foreach ($courses as $course) {
            // Ensure we have a full list element (get_courses() already returns them,
            // but be defensive in case a caller passes a plain stdClass).
            if (!($course instanceof core_course_list_element)) {
                $course = new core_course_list_element($course);
            }

            $coursecontext = context_course::instance($course->id);
            $name    = format_string($course->fullname, true, ['context' => $coursecontext]);
            $initial = \core_text::strtoupper(\core_text::substr(trim($name), 0, 1));
            if ($initial === '') {
                $initial = '?';
            }

            // Summary: strip HTML, collapse whitespace, truncate to 160 chars.
            $summary = '';
            if (!empty($course->summary)) {
                $summary = html_to_text(
                    format_text($course->summary, $course->summaryformat, ['context' => $coursecontext]),
                    0,
                    false
                );
                $summary = trim(preg_replace('/\s+/u', ' ', $summary));
                if (\core_text::strlen($summary) > 160) {
                    $summary = \core_text::substr($summary, 0, 157) . '…';
                }
            }

            $imageurl = theme_aurora_course_image_url($course);

            $coursedata[] = [
                'id'          => (int) $course->id,
                'name'        => $name,
                'initial'     => $initial,
                'url'         => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                'hassummary'  => $summary !== '',
                'summary'     => $summary,
                'hasimage'    => $imageurl !== '',
                'imageurl'    => $imageurl,
                'rotation'    => $rotations[$idx % count($rotations)],
            ];
            $idx++;
        }

        return [
            // Catalogue.
            'hascourses'   => !empty($coursedata),
            'courses'      => $coursedata,
            'introtitle'   => get_string('fp_catalog_title', 'theme_aurora'),
            'introtext'    => get_string('fp_catalog_intro', 'theme_aurora'),
            'emptylabel'   => get_string('fp_courses_empty', 'theme_aurora'),
            'viewallurl'   => (new moodle_url('/course/index.php'))->out(false),
            'viewalllabel' => get_string('fp_viewall', 'theme_aurora'),
            // Steps.
            'stepstitle'   => get_string('fp_steps_title', 'theme_aurora'),
            'steps'        => [
                [
                    'num'   => '01',
                    'title' => get_string('fp_step1_title', 'theme_aurora'),
                    'desc'  => get_string('fp_step1_desc', 'theme_aurora'),
                ],
                [
                    'num'   => '02',
                    'title' => get_string('fp_step2_title', 'theme_aurora'),
                    'desc'  => get_string('fp_step2_desc', 'theme_aurora'),
                ],
                [
                    'num'   => '03',
                    'title' => get_string('fp_step3_title', 'theme_aurora'),
                    'desc'  => get_string('fp_step3_desc', 'theme_aurora'),
                ],
            ],
            // Value section.
            'valuetitle'   => get_string('fp_value_title', 'theme_aurora'),
            'valuebody'    => get_string('fp_value_body', 'theme_aurora'),
            'note1title'   => get_string('fp_note1_title', 'theme_aurora'),
            'note1body'    => get_string('fp_note1_body', 'theme_aurora'),
            'note2title'   => get_string('fp_note2_title', 'theme_aurora'),
            'note2body'    => get_string('fp_note2_body', 'theme_aurora'),
            // CTA paper card (static yellow sticky note at bottom of landing).
            'loggedin'      => $loggedin,
            'guest'         => !$loggedin,
            'ctaloginurl'   => (new moodle_url('/login/index.php'))->out(false),
            'ctamyurl'      => (new moodle_url('/my/'))->out(false),
            'ctatitle'      => get_string('fp_cta_title', 'theme_aurora'),
            'ctabody'       => get_string('fp_cta_body', 'theme_aurora'),
            'ctaguestlabel' => get_string('fp_cta_guest', 'theme_aurora'),
            'ctamylabel'    => get_string('fp_cta_loggedin', 'theme_aurora'),
        ];
    }
}
