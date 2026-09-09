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
 * Aurora theme strings (English).
 *
 * @package    theme_aurora
 * @copyright  2026 SoftSys Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin + settings page.
$string['pluginname'] = 'Aurora';
$string['choosereadme'] = 'Aurora is a modern, gamified Moodle theme. A thin child of Boost with a left-sidebar shell, topbar and learning-progress chrome, built from the "Aprende Online — Aurora" mockup.';
$string['configtitle'] = 'Aurora settings';
$string['generalsettings'] = 'General';
$string['advancedsettings'] = 'Advanced';
$string['chromesettings'] = 'Aurora interface';

// Boost-parity settings.
$string['preset'] = 'Theme preset';
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
$string['presetfiles'] = 'Additional theme preset files';
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href="https://docs.moodle.org/dev/Boost_Presets">Boost presets</a> for information on creating and sharing your own preset files.';
$string['unaddableblocks'] = 'Unaddable blocks';
$string['unaddableblocks_desc'] = 'The blocks specified are not needed when using this theme and will not be listed in the "Add a block" menu.';
$string['logo'] = 'Logo';
$string['logo_desc'] = 'The logo shown in the sidebar header and login page. If empty, the site short name is used.';
$string['backgroundimage'] = 'Background image';
$string['backgroundimage_desc'] = 'The image to display as a background of the site. The background image you upload here will override the background image in your theme preset files.';
$string['loginbackgroundimage'] = 'Login page background image';
$string['loginbackgroundimage_desc'] = 'The image to display as the background of the login page.';
$string['brandcolor'] = 'Brand colour';
$string['brandcolor_desc'] = 'The accent colour. Drives the primary green across buttons, links and chrome.';
$string['accentcolor'] = 'Accent colour';
$string['accentcolor_desc'] = 'The sticky-note yellow used for Continuar and awards.';
$string['secondcolor'] = 'Secondary colour';
$string['secondcolor_desc'] = 'The sticky-note green used for course lanes and cards.';
$string['rawscsspre'] = 'Raw initial SCSS';
$string['rawscsspre_desc'] = 'Use this field to provide SCSS or CSS code which will be injected before everything else. Most of the time you will use this setting to define variables.';
$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet.';

// Aurora chrome settings.
$string['greeting'] = 'Greeting text';
$string['greeting_desc'] = 'The greeting shown on the home hero and topbar. The user first name is appended automatically.';
$string['default_greeting'] = 'Welcome back';
$string['showstreak'] = 'Show learning streak card';
$string['showstreak_desc'] = 'Show the learning-streak card pinned to the bottom of the sidebar.';
$string['streaktarget'] = 'Streak target (days)';
$string['streaktarget_desc'] = 'The number of days shown as the streak goal segment bar.';
$string['recommendedheading'] = 'Recommended heading';
$string['recommendedheading_desc'] = 'Heading for the recommended-courses section on the home page.';
$string['default_recommendedheading'] = 'Recommended for you';
$string['recommendedsubheading'] = 'Recommended subheading';
$string['recommendedsubheading_desc'] = 'Subheading for the recommended-courses section on the home page.';
$string['default_recommendedsubheading'] = 'Based on your recent activity';

// Aurora chrome copy (rendered by templates/renderer).
$string['streakcardtitle'] = 'Learning streak';
$string['streakcardunit'] = 'days';
$string['streakcardcta'] = 'Keep it up! {$a} more days to your weekly achievement.';
$string['streakcardhintdone'] = 'Goal reached! Great streak this week.';
$string['weeklygoal'] = 'Weekly goal (days)';
$string['weeklygoal_desc'] = 'Target active days per week for the weekly-goal ring.';
$string['showweeklygoal'] = 'Show weekly goal ring';
$string['showweeklygoal_desc'] = 'Show the weekly-goal progress ring on the dashboard.';
$string['weeklygoaltitle'] = 'Weekly goal';
$string['weeklygoaldays'] = '{$a->done} of {$a->total} days';
$string['weeklygoalcta'] = 'One more day and you hit this week\'s goal.';
$string['continuelearning'] = 'Pick up where you left off';
$string['viewall'] = 'View all';
$string['searchplaceholder'] = 'Search courses, lessons…';
$string['markcomplete'] = 'Mark as complete';
$string['nextlesson'] = 'Next lesson';
$string['inthismodule'] = 'In this module';

// Panel (Mi panel) chrome.
$string['panelsubtitle'] = 'Panel subtitle';
$string['panelsubtitle_desc'] = 'The subtitle under the "My panel" heading on the dashboard.';
$string['default_panelsubtitle'] = 'Here is your learning progress this week.';
$string['streakcount'] = 'Current streak (days)';
$string['streakcount_desc'] = 'The current learning-streak day count shown in the sidebar card and topbar pill.';

// Course-view hero (rendered above the real course format; all values REAL).
$string['herocompleted'] = '{$a->done} of {$a->total} completed';
$string['herocontinue'] = 'Continue';
$string['heroenrolled'] = '{$a} enrolled';
$string['herolessons'] = '{$a} lessons';

// Full-width frontpage hero.
$string['campusactive'] = 'Campus active';
$string['welcometocampus'] = 'welcome to your campus!';
$string['herosubtitle'] = 'Pick up where you left off and keep your learning momentum going.';
$string['featured'] = 'Featured';

// Frontpage entry (guests first — never clones /my/).
$string['frontpage_welcome_guest'] = 'Welcome to campus';
$string['frontpage_welcome_back'] = 'Hi, {$a}';
$string['frontpage_subtitle_guest'] = 'Your space to learn, practice, and grow.';
$string['frontpage_subtitle_loggedin'] = 'Your personal area has the standup board. Continue from there.';
$string['frontpage_cta_enter'] = 'Sign in';
$string['frontpage_cta_my'] = 'Go to my area';

// Login (split board).
$string['brandwordmark'] = 'Aprende';
$string['login_title'] = 'Enter the campus';
$string['login_subtitle'] = 'Your space to learn.';

// Dashboard welcome hero (rendered above the real dashboard blocks; values REAL).
$string['herodashcontinue'] = 'You are {$a->pct}% through "{$a->course}". One more push!';
$string['herodashstart'] = 'Start your next course today.';
$string['herodashsubtitle'] = 'Pick up where you left off and keep your momentum going.';
$string['herodashbrowse'] = 'Browse courses';

// My courses page header (real counts).
$string['mycoursessubtitle'] = 'Enrolled in {$a->total} courses · {$a->completed} completed';

// Sidebar nav labels (chrome links to real Moodle URLs).
$string['navhome'] = 'Home';
$string['navdashboard'] = 'My panel';
$string['navmycourses'] = 'My courses';
$string['navcatalogue'] = 'Catalogue';
$string['navcalendar'] = 'Calendar';
$string['navmessages'] = 'Messages';

// Block region names (MUST exist or the block picker silently drops the region).
$string['region-side-pre'] = 'Block drawer';
$string['region-side-post'] = 'Dashboard rail';

// Home page (Inicio) stat-row chrome.
$string['statinprogress'] = 'In progress';
$string['statcompleted'] = 'Completed';
$string['statawards'] = 'Awards';
$string['boardlabel'] = 'Learning whiteboard';
$string['boardnote_inprogress'] = '↗ Keep going ★';
$string['boardnote_completed'] = 'Nice work!';
$string['boardnote_awards'] = 'Keep growing!';
$string['boardempty'] = 'No stickies here yet.';
$string['stathours'] = 'Hours';
$string['statsinprogress_default'] = '3';
$string['statscompleted_default'] = '12';
$string['statsawards_default'] = '5';
$string['statshours_default'] = '48';
$string['continuelearningheading'] = 'Pick up where you left off';
$string['recommendedcourses'] = 'Recommended for you';
$string['statsinprogress'] = 'Stats — courses in progress';
$string['statsinprogress_desc'] = 'Number shown in the "In progress" stat on the home page.';
$string['statscompleted'] = 'Stats — completed courses';
$string['statscompleted_desc'] = 'Number shown in the "Completed" stat on the home page.';
$string['statsawards'] = 'Stats — awards earned';
$string['statsawards_desc'] = 'Number shown in the "Awards" stat on the home page.';
$string['statshours'] = 'Stats — learning hours';
$string['statshours_desc'] = 'Number shown in the "Hours" stat on the home page.';

// Frontpage landing sections (catalogue, steps, value, CTA).
// Rendered by core_course_renderer::aurora_fp_courses_context().
$string['fp_catalog_title'] = 'Available courses';
$string['fp_catalog_intro'] = 'Browse the visible courses on this campus.';
$string['fp_courses_empty'] = 'No courses available yet. Check back soon.';
$string['fp_viewall'] = 'Browse all courses';

$string['fp_steps_title'] = 'A clear path to grow';
$string['fp_step1_title'] = 'Explore';
$string['fp_step1_desc'] = 'Browse the catalogue and open the course that suits your goals.';
$string['fp_step2_title'] = 'Learn';
$string['fp_step2_desc'] = 'Work through the content and activities included in the course.';
$string['fp_step3_title'] = 'Continue';
$string['fp_step3_desc'] = 'Come back from your personal area or My courses to pick up where you left off.';

$string['fp_value_title'] = 'Learning that sticks';
$string['fp_value_body'] = 'Structured courses, real content and activities — a campus designed for learning that fits your schedule.';
$string['fp_note1_title'] = 'Your progress. Your pace.';
$string['fp_note1_body'] = 'No pressure to race ahead. Learn when and how it suits you.';
$string['fp_note2_title'] = 'A campus that keeps your place';
$string['fp_note2_body'] = 'Return whenever it suits you. Your progress is saved and the course waits for you.';

$string['fp_cta_title'] = 'Ready to start?';
$string['fp_cta_body'] = 'Sign in to access your courses and continue where you left off.';
$string['fp_cta_guest'] = 'Sign in to start learning';
$string['fp_cta_loggedin'] = 'Go to my learning area';

// Privacy.
$string['privacy:metadata'] = 'The Aurora theme does not store any personal data.';
