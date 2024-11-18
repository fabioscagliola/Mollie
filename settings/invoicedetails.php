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
 * Invoice details tab
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2017 MFreak.nl
 * @author    Luuk Verhoeven
 **/

defined('MOODLE_INTERNAL') || die();

global $ADMIN;
($ADMIN->fulltree) || die();

if (!empty($config->multi_account)) {

    $settings->add(new admin_setting_heading('enrol_coursepayment_settings', '',
        html_writer::div(get_string('setting:disabled_by_multi_account', 'enrol_coursepayment'),
            'alert alert-info')));

} else {

    $settings->add(new admin_setting_heading('enrol_coursepayment_settings', '',
        get_string('pluginname_desc', 'enrol_coursepayment')));
    $settings->add(new admin_setting_heading('enrol_coursepayment_invoicedetails',
        get_string('invoicedetails', 'enrol_coursepayment'),
        get_string('invoicedetails_desc', 'enrol_coursepayment')));

    $settings->add(new admin_setting_configtext('enrol_coursepayment/transaction_name',
        get_string('transaction_name', 'enrol_coursepayment'),
        get_string('transaction_name_desc', 'enrol_coursepayment'), '{invoice_number}',
        PARAM_TEXT));

    $settings->add(new admin_setting_configtext('enrol_coursepayment/companyname',
        get_string('companyname', 'enrol_coursepayment'), '', '',
        PARAM_TEXT));
    $settings->add(new admin_setting_configtext('enrol_coursepayment/address',
        get_string('address', 'enrol_coursepayment'), '', '',
        PARAM_TEXT));
    $settings->add(new admin_setting_configtext('enrol_coursepayment/zipcode',
        get_string('zipcode', 'enrol_coursepayment'), '', '',
        PARAM_TEXT));
    $settings->add(new admin_setting_configtext('enrol_coursepayment/place',
        get_string('place', 'enrol_coursepayment'), '', '',
        PARAM_TEXT));
    $settings->add(new admin_setting_configtext('enrol_coursepayment/kvk',
        get_string('kvk', 'enrol_coursepayment'), '', '',
        PARAM_TEXT));
    $settings->add(new admin_setting_configtext('enrol_coursepayment/btw',
        get_string('btw', 'enrol_coursepayment'), '', '',
        PARAM_TEXT));

    $settings->add(new admin_setting_heading('invoice_default', get_string('invoice_pdf', 'enrol_coursepayment'),
        get_string('invoice_desc', 'enrol_coursepayment') . '<br>' .
        enrol_coursepayment_helper::get_edit_invoice_pdf_button(1)));
}
