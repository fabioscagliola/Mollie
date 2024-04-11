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
 * Filtering for coursepayment report
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 **/

namespace enrol_coursepayment\form;

use enrol_coursepayment_gateway;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Class overview_courses_filter
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 */
class overview_courses_filter extends \moodleform {

    /**
     * Form definition
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function definition(): void {
        global $DB;

        // Get all courses.
        $courses = $DB->get_records_menu('course', [], 'fullname asc', 'id,fullname');

        $mform = &$this->_form;

        $mform->addElement('text', 'search', get_string('form:search', 'enrol_coursepayment'));
        $mform->setType('search', PARAM_TEXT);

        $array = ['' => get_string('form:make_selection', 'enrol_coursepayment')];
        $mform->addElement('select', 'courseid', get_string('form:course', 'enrol_coursepayment'), $array + $courses);
        $mform->setType('courseid', PARAM_TEXT);

        $status = [
            '' => get_string('form:make_selection', 'enrol_coursepayment'),
            enrol_coursepayment_gateway::PAYMENT_STATUS_SUCCESS => get_string('status:success', 'enrol_coursepayment'),
            enrol_coursepayment_gateway::PAYMENT_STATUS_CANCEL => get_string('status:cancel', 'enrol_coursepayment'),
            enrol_coursepayment_gateway::PAYMENT_STATUS_WAITING => get_string('status:waiting', 'enrol_coursepayment'),
            enrol_coursepayment_gateway::PAYMENT_STATUS_ABORT => get_string('status:abort', 'enrol_coursepayment'),
            enrol_coursepayment_gateway::PAYMENT_STATUS_ERROR => get_string('status:error', 'enrol_coursepayment'),
            -1 => get_string('status:no_payments', 'enrol_coursepayment'),
        ];

        $mform->addElement('select', 'status', get_string('form:payment_status', 'enrol_coursepayment'), $status);
        $mform->setType('status', PARAM_TEXT);

        $this->add_action_buttons(false, get_string('btn:filter', 'enrol_coursepayment'));
    }

}
