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
 * Multi-account setting page.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2017 MFreak.nl
 * @author    Luuk Verhoeven
 **/

defined('MOODLE_INTERNAL') || die();

//global $ADMIN;
($ADMIN->fulltree) || die();

if (!empty($config->multi_account)) {
    // Special settings for multi-account support.

    // Map profile field.
    $fields = enrol_coursepayment_helper::get_profile_fields();
    if (count($fields) == 1) {
        // Show error.
        $settings->add(new admin_setting_heading('enrol_coursepayment_message', '',
            html_writer::div(get_string('message:error_add_profile_field', 'enrol_coursepayment'), 'alert alert-danger')));
    } else {

        $settings->add(new admin_setting_configselect('enrol_coursepayment/multi_account_fieldid',
            get_string('multi_account_profile_field', 'enrol_coursepayment'),
            get_string('multi_account_profile_field_desc', 'enrol_coursepayment'), 0, $fields));

        $output = $PAGE->get_renderer('enrol_coursepayment');
        $page = new enrol_coursepayment\output\multi_account();

        $settings->add(new admin_setting_heading('multi_account',
            '', $output->render($page)));

    }

    // Add multiple mollie accounts.

    // Add multiple invoice details.

    // Set a default if profile data doesn't match.

}
