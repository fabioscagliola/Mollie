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
 * Advanced global setting page.
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

$settings->add(new admin_setting_configcheckbox('enrol_coursepayment/standalone_purchase_page',
    get_string('standalone_purchase_page', 'enrol_coursepayment'),
    get_string('standalone_purchase_page_desc', 'enrol_coursepayment'), 1));

$settings->add(new admin_setting_configselect('enrol_coursepayment/debug',
    get_string('debug', 'enrol_coursepayment'),
    get_string('debug_desc', 'enrol_coursepayment'), 0, $yesno));

// Enrol instance defaults.
$settings->add(new admin_setting_heading('enrol_coursepayment_defaults',
    get_string('enrolinstancedefaults', 'admin'),
    get_string('enrolinstancedefaults_desc', 'admin')));

$optionsyesno = [
    ENROL_INSTANCE_ENABLED => get_string('yes'),
    ENROL_INSTANCE_DISABLED => get_string('no'),
];
$settings->add(new admin_setting_configselect('enrol_coursepayment/status',
    get_string('status', 'enrol_coursepayment'),
    get_string('status_desc', 'enrol_coursepayment'), ENROL_INSTANCE_DISABLED, $optionsyesno));
$settings->add(new admin_setting_configtext('enrol_coursepayment/cost',
    get_string('cost', 'enrol_coursepayment'), '', '10,00',
    PARAM_TEXT, 4));

$coursepaymentcurrencies = enrol_get_plugin('coursepayment')->get_currencies();
$settings->add(new admin_setting_configselect('enrol_coursepayment/currency',
    get_string('currency', 'enrol_coursepayment'), '', 'EUR', $coursepaymentcurrencies));

$vatpercentages = enrol_get_plugin('coursepayment')->get_vat_percentages();
$settings->add(new admin_setting_configselect('enrol_coursepayment/vatpercentage',
    get_string('vatpercentages', 'enrol_coursepayment'), '', 21, $vatpercentages));

$obj = new stdClass();
$obj->link = $CFG->wwwroot . '/enrol/coursepayment/view/discountcode.php';
$settings->add(new admin_setting_heading('enrol_coursepayment_discount',
    get_string('enrol_coursepayment_discount', 'enrol_coursepayment'),
    get_string('enrol_coursepayment_discount_desc', 'enrol_coursepayment', $obj)));

$settings->add(new admin_setting_heading('enrol_coursepayment_agreement',
    get_string('link_agreement', 'enrol_coursepayment'), ''));

$settings->add(new admin_setting_configtext('enrol_coursepayment/link_agreement',
    get_string('link_agreement', 'enrol_coursepayment'),
    get_string('link_agreement_desc', 'enrol_coursepayment'), '', PARAM_URL));

$settings->add(new admin_setting_heading('multi_account_heading',
    get_string('multi_account_heading', 'enrol_coursepayment'),
    get_string('multi_account_desc', 'enrol_coursepayment')));

$settings->add(new admin_setting_configcheckbox('enrol_coursepayment/multi_account',
    get_string('multi_account', 'enrol_coursepayment'),
    '', 0));

$settings->add(new admin_setting_configcheckbox('enrol_coursepayment/report_include_none_payment_users',
    get_string('report_include_none_payment_users', 'enrol_coursepayment'),
    '', 1));
