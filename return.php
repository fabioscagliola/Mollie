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
 * Return page after a payment
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */

require("../../config.php");
defined('MOODLE_INTERNAL') || die();

require_once("lib.php");

require_login();

$orderid = required_param('orderid', PARAM_ALPHANUMEXT);
$gateway = required_param('gateway', PARAM_ALPHANUMEXT);
$instanceid = required_param('instanceid', PARAM_INT);

$order = $DB->get_record('enrol_coursepayment', ['orderid' => $orderid], '*', MUST_EXIST);
$course = $DB->get_record('course', ['id' => $order->courseid], '*', MUST_EXIST);
$context = context_course::instance($order->courseid);

// Not for guests this.
if (isguestuser()) {
    redirect('/');
}

$PAGE->set_course($course);
$PAGE->set_url('/enrol/coursepayment/return.php');
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('title:returnpage', 'enrol_coursepayment'));

$return = enrol_get_plugin('coursepayment')->order_valid($orderid, $gateway);

echo $OUTPUT->header();
if ($return['status'] == true) {

    if ($order->is_activity == 1) {
        echo $OUTPUT->box('<p style="text-align: center">' .
            get_string('success_enrolled_activity', 'enrol_coursepayment') .
            '</p>');

    } else {
        echo $OUTPUT->box('<p style="text-align: center">' .
            get_string('success_enrolled', 'enrol_coursepayment', $course) .
            '</p>');
    }

    // This order is paid we should enrol the user and notify.
    // Send a success message to the user.
    echo $OUTPUT->continue_button(new moodle_url('/course/view.php', ['id' => $course->id]));

} else {

    // Send a status message to user.
    echo $OUTPUT->notification($return['message']);
    echo $OUTPUT->continue_button($CFG->wwwroot . '/my');
}
echo $OUTPUT->footer();
