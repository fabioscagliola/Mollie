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
 * Multi account form
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
 * Class multiaccount
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */
class multiaccount extends \moodleform {

    /**
     * Form definition
     *
     * @throws \coding_exception
     */
    protected function definition(): void {

        $mform = &$this->_form;

        $mform->addElement('header', 'header1', get_string('form:multi_account', 'enrol_coursepayment'));

        $mform->addElement('text', 'name', get_string('form:name_multiaccount', 'enrol_coursepayment'),
            ['size' => '48']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('text', 'profile_value', get_string('form:profile_value', 'enrol_coursepayment'),
            ['size' => '48']);
        $mform->setType('profile_value', PARAM_TEXT);
        $mform->addRule('profile_value', null, 'required', null, 'client');

        $mform->addElement('header', 'header2', get_string('form:company_info', 'enrol_coursepayment'));

        $mform->addElement('text', 'company_name', get_string('form:company_name', 'enrol_coursepayment'),
            ['size' => '48']);
        $mform->setType('company_name', PARAM_TEXT);
        $mform->addRule('company_name', null, 'required', null, 'client');

        $mform->addElement('text', 'address', get_string('form:address', 'enrol_coursepayment'),
            ['size' => '48']);
        $mform->setType('address', PARAM_TEXT);
        $mform->addRule('address', null, 'required', null, 'client');

        $mform->addElement('text', 'place', get_string('form:place', 'enrol_coursepayment'),
            ['size' => '48']);
        $mform->setType('place', PARAM_TEXT);
        $mform->addRule('place', null, 'required', null, 'client');

        $mform->addElement('text', 'zipcode', get_string('form:zipcode', 'enrol_coursepayment'),
            ['size' => '48']);
        $mform->setType('zipcode', PARAM_TEXT);
        $mform->addRule('zipcode', null, 'required', null, 'client');

        $mform->addElement('text', 'btw', get_string('form:btw', 'enrol_coursepayment'),
            ['size' => '48']);
        $mform->setType('btw', PARAM_TEXT);
        $mform->addRule('btw', null, 'required', null, 'client');

        $mform->addElement('text', 'kvk', get_string('form:kvk', 'enrol_coursepayment'),
            ['size' => '48']);
        $mform->setType('kvk', PARAM_TEXT);
        $mform->addRule('kvk', null, 'required', null, 'client');

        $mform->addElement('header', 'header3', get_string('form:mollie', 'enrol_coursepayment'));

        $mform->addElement('text', 'gateway_mollie_apikey', get_string('gateway_mollie_apikey',
            'enrol_coursepayment'), ['size' => '48']);
        $mform->setType('gateway_mollie_apikey', PARAM_TEXT);
        $mform->addRule('gateway_mollie_apikey', null, 'required', null, 'client');

        $mform->addElement('text', 'gateway_mollie_partner_id', get_string('gateway_mollie_partner_id',
            'enrol_coursepayment'), ['size' => '48']);
        $mform->setType('gateway_mollie_partner_id', PARAM_TEXT);
        $mform->addRule('gateway_mollie_partner_id', null, 'required', null, 'client');

        $mform->addElement('checkbox', 'gateway_mollie_debug', get_string('gateway_mollie_debug',
            'enrol_coursepayment'));
        $mform->addElement('checkbox', 'gateway_mollie_sandbox', get_string('gateway_mollie_sandbox',
            'enrol_coursepayment'));

        $this->add_action_buttons(true, get_string('form:save', 'enrol_coursepayment'));
    }

}
