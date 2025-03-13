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
 * Adds new instance of enrol_coursepayment to specified course or edits current instance.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 **/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Class enrol_cousrepayment_edit_form
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 **/
class enrol_coursepayment_edit_form extends moodleform {

    /**
     * Form definition.
     *
     * @return void
     */
    public function definition(): void {
        $mform = $this->_form;

        [$instance, $plugin, $context] = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_coursepayment'));

        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'));
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('text', 'customtext1', get_string('instancedesc', 'enrol_coursepayment'));
        $mform->setType('customtext1', PARAM_TEXT);

        $mform->addElement('text', 'customtext2', get_string('customtext2', 'enrol_coursepayment'));
        $mform->setType('customtext2', PARAM_TEXT);

        $options = [
            ENROL_INSTANCE_ENABLED => get_string('yes'),
            ENROL_INSTANCE_DISABLED => get_string('no'),
        ];
        $mform->addElement('select', 'status', get_string('status', 'enrol_coursepayment'), $options);
        $mform->setDefault('status', $plugin->get_config('status'));

        $mform->addElement('text', 'cost', get_string('cost', 'enrol_coursepayment'), ['size' => 4]);
        $mform->setType('cost', PARAM_RAW); // Use unformat_float to get real value.

        $mform->setDefault('cost', format_float((float)str_replace(',', '.', $plugin->get_config('cost')), 2, true));

        $currencies = $plugin->get_currencies();
        $mform->addElement('select', 'currency', get_string('currency', 'enrol_coursepayment'),
            $currencies);
        $mform->setDefault('currency', $plugin->get_config('currency'));

        $vatpercentages = $plugin->get_vat_percentages();
        $mform->addElement('select', 'customint1', get_string('vatpercentages', 'enrol_coursepayment'),
            $vatpercentages);
        $mform->setDefault('customint1', $plugin->get_config('vatpercentage'));

        if ($instance->id) {
            $roles = get_default_enrol_roles($context, $instance->roleid);
        } else {
            $roles = get_default_enrol_roles($context, $plugin->get_config('roleid'));
        }
        $mform->addElement('select', 'roleid', get_string('assignrole', 'enrol_coursepayment'),
            $roles);
        $mform->setDefault('roleid', $plugin->get_config('roleid'));

        $mform->addElement('duration', 'enrolperiod', get_string('enrolperiod', 'enrol_coursepayment'), [
            'optional' => true,
            'defaultunit' => 86400,
        ]);
        $mform->setDefault('enrolperiod', $plugin->get_config('enrolperiod'));
        $mform->addHelpButton('enrolperiod', 'enrolperiod', 'enrol_coursepayment');

        $mform->addElement('date_time_selector', 'enrolstartdate', get_string('enrolstartdate',
            'enrol_coursepayment'), ['optional' => true]);

        $mform->setDefault('enrolstartdate', 0);
        $mform->addHelpButton('enrolstartdate', 'enrolstartdate', 'enrol_coursepayment');

        $mform->addElement('date_time_selector', 'enrolenddate', get_string('enrolenddate',
            'enrol_coursepayment'), ['optional' => true]);
        $mform->setDefault('enrolenddate', 0);
        $mform->addHelpButton('enrolenddate', 'enrolenddate', 'enrol_coursepayment');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $options = [
            0 => get_string('no'),
            1 => get_string('expirynotifyenroller', 'core_enrol'),
            2 => get_string('expirynotifyall', 'core_enrol'),
        ];
        $mform->addElement('select', 'expirynotify', get_string('expirynotify', 'core_enrol'), $options);
        $mform->addHelpButton('expirynotify', 'expirynotify', 'core_enrol');

        $mform->addElement('duration', 'expirythreshold', get_string('expirythreshold', 'core_enrol'), [
            'optional' => false,
            'defaultunit' => 86400,
        ]);
        $mform->addHelpButton('expirythreshold', 'expirythreshold', 'core_enrol');
        $mform->disabledIf('expirythreshold', 'expirynotify', 'eq', 0);

        if (enrol_accessing_via_instance($instance)) {
            $mform->addElement('static', 'selfwarn', get_string('instanceeditselfwarning', 'core_enrol'),
                get_string('instanceeditselfwarningtext', 'core_enrol'));
        }

        $this->add_action_buttons(true, ($instance->id ? null : get_string('addinstance', 'enrol')));

        $this->set_data($instance);
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     *
     * @return array
     */
    public function validation($data, $files): array {

        $errors = parent::validation($data, $files);

        if (!empty($data['enrolenddate']) && $data['enrolenddate'] < $data['enrolstartdate']) {
            $errors['enrolenddate'] = get_string('enrolenddaterror', 'enrol_coursepayment');
        }

        $cost = str_replace(',', '.', $data['cost']);
        if (!is_numeric($cost)) {
            $errors['cost'] = get_string('costerror', 'enrol_coursepayment');
        }

        if ($data['expirynotify'] > 0 && $data['expirythreshold'] < 86400) {
            $errors['expirythreshold'] = get_string('errorthresholdlow', 'core_enrol');
        }

        return $errors;
    }

}
