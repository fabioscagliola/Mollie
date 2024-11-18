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
 * discount code form
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */

namespace enrol_coursepayment\form;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Class discountcode
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */
class discountcode extends \moodleform {

    /**
     * Form definition
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function definition(): void {
        global $DB;

        $mform = &$this->_form;

        $mform->addElement('header', 'header1', get_string('form:discountcode', 'enrol_coursepayment'));

        $mform->addElement('text', 'code', get_string('form:code', 'enrol_coursepayment'),
            ['size' => '48']);
        $mform->setType('code', PARAM_ALPHANUMEXT);
        $mform->addRule('code', null, 'required', null, 'client');

        $list = [0 => get_string('form:allcourses', 'enrol_coursepayment')];
        $qr = $DB->get_recordset('course', null, 'fullname ASC', 'id,fullname');
        foreach ($qr as $row) {
            $list[$row->id] = $row->fullname;
        }
        $qr->close();
        $mform->addElement('select', 'courseid', get_string('course'), $list);
        $mform->addRule('courseid', null, 'required', null, 'client');

        $mform->addElement('date_selector', 'start_time', get_string('form:start_time',
            'enrol_coursepayment'));
        $mform->setDefault('start_time', time());
        $mform->addRule('start_time', null, 'required', null, 'client');

        $mform->addElement('date_selector', 'end_time', get_string('form:end_time',
            'enrol_coursepayment'));
        $mform->setDefault('end_time', strtotime('+6 months'));
        $mform->addRule('end_time', null, 'required', null, 'client');

        $mform->addElement('text', 'amount', get_string('form:amount',
            'enrol_coursepayment'));
        $mform->setDefault('amount', 0);
        $mform->setType('amount', PARAM_TEXT);

        $mform->addElement('text', 'percentage', get_string('form:percentage',
            'enrol_coursepayment'));
        $mform->setDefault('percentage', '0.00000');
        $mform->setType('percentage', PARAM_TEXT);

        $mform->disabledIf('percentage', 'amount', 'neq', '0');
        $mform->disabledIf('amount', 'percentage', 'neq', '0.00000');

        $this->add_action_buttons(true, get_string('form:save', 'enrol_coursepayment'));
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     *
     * @return array
     * @throws \coding_exception
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        if (!empty($data['amount'])) {
            $amount = trim(str_replace(',', '.', $data['amount']));
            if (!is_numeric($amount)) {
                $errors['amount'] = get_string('error:price_wrongformat', 'enrol_coursepayment');

                return $errors;
            } else if ($amount <= 0) {
                $errors['amount'] = get_string('error:number_to_low', 'enrol_coursepayment');

                return $errors;
            }
        } else {
            $percentage = trim(str_replace(',', '.', $data['percentage']));
            if ($percentage <= 0) {
                $errors['percentage'] = get_string('error:number_to_low', 'enrol_coursepayment');

                return $errors;
            } else if (!is_numeric($percentage)) {
                $errors['percentage'] = get_string('error:price_wrongformat', 'enrol_coursepayment');

                return $errors;
            }
        }

        return $errors;
    }

}
