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
 * Coursepayment report
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 **/

require_once(__DIR__ . '/../../../config.php');
defined('MOODLE_INTERNAL') || die();

$courseid = required_param('id', PARAM_INT);
$action = optional_param('action', false, PARAM_ALPHA);

$PAGE->set_url('/enrol/coursepayment/view/report.php', [
    'id' => $courseid,
    'action' => $action,
]);

// Get course.
$parentcourse = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

require_login($parentcourse, false);

$coursecontext = context_course::instance($courseid);
require_capability('enrol/coursepayment:report', $coursecontext);

// Page layout.
$PAGE->set_heading($SITE->fullname);
$PAGE->set_pagelayout('base');
$PAGE->set_title(get_string('heading:report', 'enrol_coursepayment'));
$PAGE->navbar->add(ucfirst($parentcourse->fullname), new moodle_url('/course/view.php', ['id' => $parentcourse->id]));
$PAGE->navbar->add(get_string('heading:report', 'enrol_coursepayment'));
$PAGE->requires->css('/enrol/coursepayment/styles.css');

// Get current params.
$params = $PAGE->url->params();

// Form.
$form = new \enrol_coursepayment\form\overview_courses_filter($PAGE->url);

switch ($action) {

    default:
        echo $OUTPUT->header();

        // Filtering.
        $form->display();

        // Table.
        \enrol_coursepayment\report::table_overview_courses($form->get_data());

        echo $OUTPUT->footer();
}
