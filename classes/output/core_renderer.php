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

use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Aurora core renderer. Thin child of theme_boost's renderer.
 *
 * Aurora is a pure skin: it adds NO custom layout or page chrome. This renderer
 * only:
 *  - injects the Jost web font (the Aurora type face),
 *  - resolves the favicon against the child theme, and
 *  - tags <body> with the `aurora-shell` class so post.scss can scope its
 *    restyle rules to Aurora pages.
 *
 * No page, course, calendar or block data is ever produced here.
 *
 * @package    theme_aurora
 * @copyright  2026 SoftSys Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Add the functional course-search control shown in Aurora's header.
     *
     * Core still owns notifications and messaging; this only prepends a real
     * search destination for authenticated users.
     *
     * @return string
     */
    public function navbar_plugin_output() {
        $output = parent::navbar_plugin_output();

        if (isloggedin() && !isguestuser()) {
            $searchurl = new moodle_url('/course/search.php');
            $searchicon = $this->pix_icon('i/search', get_string('search'), 'moodle');
            $searchlink = \html_writer::link($searchurl, $searchicon, [
                'class' => 'aurora-navbar-search nav-link icon-no-margin',
                'aria-label' => get_string('search'),
                'title' => get_string('search'),
            ]);
            $output = $searchlink . $output;
        }

        return $output;
    }

    /**
     * Inject the Jost web font (the Aurora type face). Done here
     * rather than via an @import in SCSS, which ScssPhp parses as a file import.
     *
     * @return string
     */
    public function standard_head_html() {
        $output = parent::standard_head_html();
        $output .= '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        $output .= '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        $output .= '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?'
            . 'family=Jost:wght@400;500;600;700&family=Nanum+Brush+Script&family=Nanum+Pen+Script&display=swap">' . "\n";
        return $output;
    }

    /**
     * Resolve the favicon against the active (Aurora) theme rather than boost.
     *
     * @return moodle_url|string The favicon URL.
     */
    public function favicon() {
        $logo = null;
        if (!during_initial_install()) {
            $logo = $this->page->theme->setting_file_url('favicon', 'favicon');
        }
        if (empty($logo)) {
            return parent::favicon();
        }
        return $logo;
    }

    /**
     * Append the `aurora-shell` class to every page's <body> so the Aurora
     * skin (post.scss) scopes cleanly to this theme without touching Boost's
     * native layout DOM.
     *
     * @param array|string $additionalclasses Extra classes from the layout file.
     * @return string The body tag id + class attributes.
     */
    public function body_attributes($additionalclasses = []) {
        if (!is_array($additionalclasses)) {
            $additionalclasses = explode(' ', $additionalclasses);
        }
        $additionalclasses[] = 'aurora-shell';
        return parent::body_attributes($additionalclasses);
    }

    /**
     * Prepend the Aurora welcome hero on the dashboard. The hero lives INSIDE
     * the content column (Boost renders full_header above #region-main-box), so
     * it never touches the native navbar, course-index drawer or block drawers.
     *
     * Two hero variants:
     *  - frontpage  → frontpage_welcome (guest-first welcome sticky; never
     *                 clones the /my/ standup board).
     *  - mydashboard → dashboard_hero (standup whiteboard + sticky lanes).
     *
     * @return string
     */
    public function full_header() {
        $header = parent::full_header();
        $layout = $this->page->pagelayout;

        // Frontpage is the campus entry (mostly guests) — never clone /my/ board.
        // Guest/logged-in chrome: theme_aurora/frontpage_welcome (whiteboard world).
        if ($layout === 'frontpage') {
            try {
                $ctx = $this->aurora_frontpage_welcome_context();
                $welcome = $this->render_from_template('theme_aurora/frontpage_welcome', $ctx);
                return $welcome . $header;
            } catch (\Throwable $e) {
                return $header;
            }
        }

        // Dashboard (/my/): compact hero + 3 stat cards.
        if ($layout === 'mydashboard'
                && isloggedin() && !isguestuser()) {
            try {
                $hero = $this->render_from_template('theme_aurora/dashboard_hero',
                    $this->aurora_dashboard_hero_context());
                return $hero . $header;
            } catch (\Throwable $e) {
                // A hero must never break the page; fall back to the plain header.
                return $header;
            }
        }

        // Course main page: replace the plain heading with the Aurora course hero
        // (real course data + completion ring). Editing teachers keep the native
        // header so the course controls stay unobstructed.
        if ($layout === 'course' && isloggedin() && !isguestuser()) {
            try {
                $ctx = $this->aurora_course_hero_context();
                if ($ctx !== false) {
                    return $this->render_from_template('theme_aurora/course_hero', $ctx);
                }
            } catch (\Throwable $e) {
                return $header;
            }
        }

        // My courses page: a clean branded section header with a real enrolment
        // summary, replacing the plain "My courses" heading.
        if ($layout === 'mycourses' && isloggedin() && !isguestuser()) {
            try {
                return $this->render_from_template('theme_aurora/page_header',
                    $this->aurora_courses_header_context());
            } catch (\Throwable $e) {
                return $header;
            }
        }

        return $header;
    }

    /**
     * Build the standup whiteboard context from REAL learner data.
     *
     * Continuar sticky = highest in-progress completion. Lanes list real
     * enrolments (in progress / completed) and issued badges. No invented %.
     *
     * @return array
     */
    public function aurora_dashboard_hero_context(): array {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');
        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->libdir . '/badgeslib.php');

        $continue = null;
        $inprogress = [];
        $completed = [];
        $lanecap = 3;

        try {
            $courses = enrol_get_my_courses(['id', 'fullname', 'visible', 'enablecompletion', 'cacherev'],
                'visible DESC, fullname ASC');
            foreach ($courses as $course) {
                if ($course->id == SITEID) {
                    continue;
                }
                $pct = \core_completion\progress::get_course_progress_percentage($course, $USER->id);
                if ($pct === null) {
                    $pct = 0;
                }
                $pct = (int) round($pct);
                $item = [
                    'name' => format_string($course->fullname, true,
                        ['context' => \context_course::instance($course->id)]),
                    'url' => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                    'pct' => $pct,
                    'id' => (int) $course->id,
                ];
                if ($pct >= 100) {
                    $completed[] = $item;
                } else {
                    $inprogress[] = $item;
                    if ($continue === null || $pct > $continue['pct']) {
                        $continue = $item;
                    }
                }
            }
            // Prefer recently progressed courses at the top of the lane.
            usort($inprogress, static function (array $a, array $b): int {
                return $b['pct'] <=> $a['pct'];
            });
        } catch (\Throwable $e) {
            $continue = null;
            $inprogress = [];
            $completed = [];
        }

        // Keep Continuar sticky distinct from the "En progreso" lane stickies.
        if ($continue !== null) {
            $inprogress = array_values(array_filter($inprogress, static function (array $item) use ($continue): bool {
                return $item['id'] !== $continue['id'];
            }));
        }
        $inprogresstotal = count($inprogress);
        $completedtotal = count($completed);
        $inprogress = array_slice($inprogress, 0, $lanecap);
        $completed = array_slice($completed, 0, $lanecap);

        $awards = [];
        $awardstotal = 0;
        try {
            $badges = badges_get_user_badges($USER->id, 0, 0, 100);
            $awardstotal = is_array($badges) || $badges instanceof \Traversable ? count($badges) : 0;
            if ($badges instanceof \Traversable) {
                $badges = iterator_to_array($badges);
            }
            $badges = array_slice(array_values($badges ?: []), 0, $lanecap);
            foreach ($badges as $badge) {
                $badgeobj = new \badge($badge->id);
                $imageurl = moodle_url::make_pluginfile_url(
                    $badgeobj->get_context()->id,
                    'badges',
                    'badgeimage',
                    $badge->id,
                    '/',
                    'f1'
                )->out(false);
                $desc = trim(html_to_text($badge->description ?? '', 0));
                if (\core_text::strlen($desc) > 72) {
                    $desc = \core_text::substr($desc, 0, 69) . '…';
                }
                $awards[] = [
                    'name' => format_string($badge->name),
                    'description' => $desc,
                    'imageurl' => $imageurl,
                    'url' => (new moodle_url('/badges/badge.php', ['hash' => $badge->uniquehash]))->out(false),
                ];
            }
        } catch (\Throwable $e) {
            $awards = [];
            $awardstotal = 0;
        }

        $hascontinue = ($continue !== null);

        return [
            'boardlabel' => get_string('boardlabel', 'theme_aurora'),
            'hascontinue' => $hascontinue,
            'title' => $hascontinue
                ? $continue['name']
                : get_string('herodashstart', 'theme_aurora'),
            'subtitle' => $hascontinue ? '' : get_string('herodashsubtitle', 'theme_aurora'),
            'pct' => $hascontinue ? $continue['pct'] : 0,
            'ctaurl' => $hascontinue
                ? $continue['url']
                : (new moodle_url('/my/courses.php'))->out(false),
            'ctalabel' => $hascontinue
                ? get_string('herocontinue', 'theme_aurora')
                : get_string('herodashbrowse', 'theme_aurora'),
            'inprogresslabel' => get_string('statinprogress', 'theme_aurora'),
            'completedlabel' => get_string('statcompleted', 'theme_aurora'),
            'awardslabel' => get_string('statawards', 'theme_aurora'),
            'inprogressnote' => get_string('boardnote_inprogress', 'theme_aurora'),
            'completednote' => get_string('boardnote_completed', 'theme_aurora'),
            'awardsnote' => get_string('boardnote_awards', 'theme_aurora'),
            'emptylane' => get_string('boardempty', 'theme_aurora'),
            'inprogress' => $inprogress,
            'completed' => $completed,
            'awards' => $awards,
            'inprogresscount' => $inprogresstotal,
            'completedcount' => $completedtotal,
            'awardscount' => $awardstotal,
            'trayurl' => $this->image_url('whiteboard/tray-dashboard', 'theme_aurora')->out(false),
            // Keep legacy keys for any leftover callers.
            'mycoursesurl' => (new moodle_url('/my/courses.php'))->out(false),
            'mycourseslabel' => get_string('navmycourses', 'theme_aurora'),
        ];
    }

    /**
     * Build the full-width frontpage hero context from REAL learner data.
     *
     * Same data sources as the dashboard hero (enrol_get_my_courses +
     * core_completion + badge_issued) so the public home and /my/ never drift,
     * plus the enrolled-course list (with per-course completion % and overview
     * image) that feeds the bento grid. The hero "Continue" CTA targets the
     * in-progress course with the highest completion.
     *
     * @return array
     */
    public function aurora_frontpage_hero_context(): array {
        global $USER, $CFG, $DB;
        require_once($CFG->dirroot . '/lib/enrollib.php');
        require_once($CFG->libdir . '/completionlib.php');

        $firstname = (isloggedin() && !isguestuser()) ? $USER->firstname : '';

        $inprogress = 0;
        $completed = 0;
        $continue = null;
        $coursesforbento = [];

        try {
            $courses = enrol_get_my_courses(
                ['id', 'fullname', 'shortname', 'visible', 'enablecompletion', 'cacherev', 'summary', 'summaryformat', 'category'],
                'visible DESC, fullname ASC',
                0,
                []
            );
            foreach ($courses as $course) {
                if ($course->id == SITEID) {
                    continue;
                }
                $coursecontext = \context_course::instance($course->id);
                $pct = \core_completion\progress::get_course_progress_percentage($course, $USER->id);
                if ($pct === null) {
                    $pct = 0;
                }
                $pct = (int) round($pct);
                $isdone = ($pct >= 100);
                if ($isdone) {
                    $completed++;
                } else {
                    $inprogress++;
                }
                if (!$isdone && ($continue === null || $pct > $continue['pct'])) {
                    $continue = [
                        'url' => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                        'pct' => $pct,
                    ];
                }
                // Build the bento entry (limit to 6 cards for the home grid).
                if (count($coursesforbento) < 6) {
                    $categoryname = '';
                    if (!empty($course->category)) {
                        $catname = $DB->get_field('course_categories', 'name', ['id' => $course->category]);
                        if ($catname !== false) {
                            $categoryname = format_string($catname, true, ['context' => $coursecontext]);
                        }
                    }
                    // Course overview image (real — overviewfiles).
                    $imageurl = '';
                    try {
                        $listelement = new \core_course_list_element($course);
                        foreach ($listelement->get_course_overviewfiles() as $file) {
                            if ($file->is_valid_image()) {
                                $imageurl = moodle_url::make_pluginfile_url(
                                    $file->get_contextid(),
                                    $file->get_component(),
                                    $file->get_filearea(),
                                    $file->get_itemid(),
                                    $file->get_filepath(),
                                    $file->get_filename()
                                )->out(false);
                                break;
                            }
                        }
                    } catch (\Throwable $e) {
                        $imageurl = '';
                    }
                    $coursesforbento[] = [
                        'id' => $course->id,
                        'fullname' => format_string($course->fullname, true, ['context' => $coursecontext]),
                        'category' => $categoryname,
                        'hascategory' => $categoryname !== '',
                        'progress' => $pct,
                        'hasprogress' => $pct > 0,
                        'imageurl' => $imageurl,
                        'viewurl' => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                        // First in-progress course is the featured (2x2) card.
                        'featured' => (!$isdone && $continue !== null
                            && $continue['pct'] === $pct && empty(array_filter($coursesforbento, function ($c) { return !empty($c['featured']); }))),
                    ];
                }
            }
        } catch (\Throwable $e) {
            $continue = null;
            $coursesforbento = [];
        }

        $awards = 0;
        try {
            $awards = $DB->count_records('badge_issued', ['userid' => $USER->id]);
        } catch (\Throwable $e) {
            $awards = 0;
        }

        $hascontinue = ($continue !== null);

        return [
            'greeting' => get_string('default_greeting', 'theme_aurora'),
            'firstname' => $firstname,
            'hasfirstname' => $firstname !== '',
            // Hero CTAs.
            'mycoursesurl' => (new moodle_url('/my/courses.php'))->out(false),
            'mycourseslabel' => get_string('navmycourses', 'theme_aurora'),
            'hascontinue' => $hascontinue,
            'continueurl' => $hascontinue ? $continue['url'] : '',
            'continuelabel' => get_string('herocontinue', 'theme_aurora'),
            // Stats grid (4 cards — the 4th is awards so the grid is balanced).
            'stats' => [
                ['value' => $inprogress, 'label' => get_string('statinprogress', 'theme_aurora'), 'variant' => 'primary'],
                ['value' => $completed, 'label' => get_string('statcompleted', 'theme_aurora'), 'variant' => 'success'],
                ['value' => $awards, 'label' => get_string('statawards', 'theme_aurora'), 'variant' => 'accent'],
            ],
            // Bento grid (real enrolled courses).
            'courses' => $coursesforbento,
            'hascourses' => !empty($coursesforbento),
        ];
    }

    /**
     * Frontpage welcome chrome for guests (and a light CTA for logged-in users).
     * Never clones the /my/ standup board — that job lives on Área personal.
     *
     * @return array
     */
    public function aurora_frontpage_welcome_context(): array {
        global $USER, $CFG, $SITE;

        $loggedin = isloggedin() && !isguestuser();
        $sitename = format_string($SITE->fullname);
        $loginurl = (new moodle_url('/login/index.php'))->out(false);
        $myurl = (new moodle_url('/my/'))->out(false);

        $firstname = $loggedin ? $USER->firstname : '';

        return [
            'sitename' => $sitename,
            'loggedin' => $loggedin,
            'guest' => !$loggedin,
            'firstname' => $firstname,
            'hasfirstname' => $firstname !== '',
            'title' => $loggedin
                ? get_string('frontpage_welcome_back', 'theme_aurora', $firstname ?: $sitename)
                : get_string('frontpage_welcome_guest', 'theme_aurora'),
            'subtitle' => $loggedin
                ? get_string('frontpage_subtitle_loggedin', 'theme_aurora')
                : get_string('frontpage_subtitle_guest', 'theme_aurora'),
            'ctaurl' => $loggedin ? $myurl : $loginurl,
            'ctalabel' => $loggedin
                ? get_string('frontpage_cta_my', 'theme_aurora')
                : get_string('frontpage_cta_enter', 'theme_aurora'),
            'secondaryurl' => $loggedin
                ? (new moodle_url('/my/courses.php'))->out(false)
                : '',
            'secondarylabel' => $loggedin ? get_string('navmycourses', 'theme_aurora') : '',
            'hassecondary' => $loggedin,
            // Physical materials reproduced from the locked frontpage comp.
            'paperurl' => $this->image_url('whiteboard/sticky-paper-frontpage', 'theme_aurora')->out(false),
            'trayurl' => $this->image_url('whiteboard/tray-frontpage', 'theme_aurora')->out(false),
        ];
    }

    /**
     * Split-board login chrome. Form fields stay in {{{ output.main_content }}}.
     *
     * @return array
     */
    public function aurora_login_context(): array {
        global $SITE;

        return [
            'brand' => get_string('brandwordmark', 'theme_aurora'),
            'homeurl' => (new moodle_url('/'))->out(false),
            'title' => get_string('login_title', 'theme_aurora'),
            'titleimageurl' => $this->image_url('whiteboard/login-title', 'theme_aurora')->out(false),
            'subtitle' => get_string('login_subtitle', 'theme_aurora'),
            'paperurl' => $this->image_url('whiteboard/paper-login', 'theme_aurora')->out(false),
            'trayurl' => $this->image_url('whiteboard/tray-frontpage', 'theme_aurora')->out(false),
        ];
    }

    /**
     * Build the Aurora course-view hero context from REAL course data.
     *
     * Reads live Moodle objects: course fullname / summary / category, the
     * viewable lesson count from modinfo, the active enrolment count, and the
     * REAL completion ring + "Continue" target for the CURRENT user. Returns
     * false off real courses (site front page) and while editing, so the native
     * course controls stay unobstructed for teachers.
     *
     * @return array|false
     */
    public function aurora_course_hero_context() {
        global $USER, $CFG, $DB;

        $course = $this->page->course;
        if (empty($course) || $course->id == SITEID || $this->page->user_is_editing()) {
            return false;
        }

        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->libdir . '/completionlib.php');

        $coursecontext = \context_course::instance($course->id);
        $fullname = format_string($course->fullname, true, ['context' => $coursecontext]);

        // Summary -> tidy one-paragraph clamp (real).
        $summary = '';
        if (!empty($course->summary)) {
            $summary = html_to_text(
                format_text($course->summary, $course->summaryformat, ['context' => $coursecontext]), 0, false);
            $summary = trim(preg_replace('/\s+/u', ' ', $summary));
        }

        // Category name (real, read directly so it always resolves).
        $categoryname = '';
        if (!empty($course->category)) {
            $catname = $DB->get_field('course_categories', 'name', ['id' => $course->category]);
            if ($catname !== false) {
                $categoryname = format_string($catname, true, ['context' => $coursecontext]);
            }
        }

        $initial = \core_text::strtoupper(\core_text::substr(trim($fullname), 0, 1));

        $imageurl = theme_aurora_course_image_url($course);

        // Viewable lesson count + active enrolment count (real).
        $lessoncount = 0;
        $modinfo = get_fast_modinfo($course);
        foreach ($modinfo->cms as $cm) {
            if ($cm->uservisible && $cm->has_view()) {
                $lessoncount++;
            }
        }
        $enrolledcount = count_enrolled_users($coursecontext);

        // Completion ring for the CURRENT user (real).
        $completion = new \completion_info($course);
        $hascompletion = $completion->is_enabled() && isloggedin() && !isguestuser();
        $pct = 0;
        $done = 0;
        $total = 0;
        $continueurl = (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false);

        if ($hascompletion) {
            $activities = $completion->get_activities();
            $total = count($activities);
            $nexturl = null;
            foreach ($activities as $activity) {
                $data = $completion->get_data($activity, true, $USER->id);
                $complete = in_array((int) $data->completionstate,
                    [COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS], true);
                if ($complete) {
                    $done++;
                } else if ($nexturl === null) {
                    $cm = $modinfo->get_cm($activity->id);
                    if ($cm && $cm->uservisible && $cm->url) {
                        $nexturl = $cm->url->out(false);
                    }
                }
            }
            $pct = $total > 0 ? (int) round(($done / $total) * 100) : 0;
            if ($nexturl !== null) {
                $continueurl = $nexturl;
            }
        }

        // SVG ring geometry: r=33 => circumference ~207.3.
        $ringcircumference = round(2 * M_PI * 33, 1);
        $ringoffset = round($ringcircumference * (1 - $pct / 100), 1);

        return [
            'fullname' => $fullname,
            'initial' => $initial,
            'hasimage' => $imageurl !== '',
            'imageurl' => $imageurl,
            'hassummary' => $summary !== '',
            'summary' => $summary,
            'hascategory' => $categoryname !== '',
            'category' => $categoryname,
            'lessoncount' => $lessoncount,
            'haslessons' => $lessoncount > 0,
            'lessonslabel' => get_string('herolessons', 'theme_aurora', $lessoncount),
            'enrolledcount' => $enrolledcount,
            'hasenrolled' => $enrolledcount > 0,
            'enrolledlabel' => get_string('heroenrolled', 'theme_aurora', $enrolledcount),
            // Completion ring.
            'hascompletion' => $hascompletion && $total > 0,
            'completionpct' => $pct,
            'completedlabel' => get_string('herocompleted', 'theme_aurora', ['done' => $done, 'total' => $total]),
            'ringcircumference' => $ringcircumference,
            'ringoffset' => $ringoffset,
            'continueurl' => $continueurl,
            'continuelabel' => get_string('herocontinue', 'theme_aurora'),
            // Breadcrumb chrome.
            'mycoursesurl' => (new moodle_url('/my/courses.php'))->out(false),
            'mycourseslabel' => get_string('navmycourses', 'theme_aurora'),
        ];
    }

    /**
     * Build the "My courses" page header context from REAL enrolment data: the
     * total enrolled-course count and how many are completed for the current
     * user (core_completion API).
     *
     * @return array
     */
    public function aurora_courses_header_context(): array {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');
        require_once($CFG->libdir . '/completionlib.php');

        $total = 0;
        $completed = 0;
        try {
            $courses = enrol_get_my_courses(['id', 'enablecompletion', 'cacherev']);
            foreach ($courses as $course) {
                if ($course->id == SITEID) {
                    continue;
                }
                $total++;
                $pct = \core_completion\progress::get_course_progress_percentage($course, $USER->id);
                if ($pct !== null && (int) round($pct) >= 100) {
                    $completed++;
                }
            }
        } catch (\Throwable $e) {
            $total = 0;
            $completed = 0;
        }

        return [
            'title' => get_string('navmycourses', 'theme_aurora'),
            'subtitle' => get_string('mycoursessubtitle', 'theme_aurora',
                ['total' => $total, 'completed' => $completed]),
        ];
    }
}
