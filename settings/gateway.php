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
 * Gateway global setting page.
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

    $settings->add(new admin_setting_heading('enrol_coursepayment_gateway_mollie',
        get_string('gateway_mollie', 'enrol_coursepayment'),
        get_string('gateway_mollie_desc', 'enrol_coursepayment')));

    $settings->add(new admin_setting_heading('enrol_coursepayment_register', '',
        '<aside style="border: 1px solid red;padding: 3px">' . get_string('gateway_mollie_link',
            'enrol_coursepayment',
            (object)['link' => 'https://my.mollie.com/dashboard/signup']) . '</aside><hr/>'));

    $settings->add(new admin_setting_configselect('enrol_coursepayment/gateway_mollie_enabled',
        get_string('enabled', 'enrol_coursepayment'),
        get_string('enabled_desc', 'enrol_coursepayment'), 1, $yesno));

    // Normal only for NEW installation from > 2020011500.
    if (enrol_coursepayment_helper::requires_mollie_connect()) {
        // Disable API_key till this is filled.
        $settings->add(new admin_setting_configempty('mollieconnect', 'mollieconnect',
            enrol_coursepayment_helper::get_mollie_connect_link()));
    }

    $settings->add(new admin_setting_configtext('enrol_coursepayment/gateway_mollie_apikey',
        get_string('gateway_mollie_apikey', 'enrol_coursepayment'), '', '',
        PARAM_ALPHANUMEXT));

    $settings->add(new admin_setting_configselect('enrol_coursepayment/gateway_mollie_sandbox',
        get_string('sandbox', 'enrol_coursepayment'),
        get_string('sandbox_desc', 'enrol_coursepayment'), 0, $yesno));

    $settings->add(new admin_setting_configselect('enrol_coursepayment/gateway_mollie_external_connector',
        get_string('gateway_mollie_external_connector', 'enrol_coursepayment'),
        get_string('gateway_mollie_external_connector_desc', 'enrol_coursepayment'), 0, $yesno));

    // Check if gateway_mollie_external_connector is enabled.
    if (!empty($config->gateway_mollie_external_connector)) {
        $settings->add(new admin_setting_configtext('enrol_coursepayment/gateway_mollie_partner_id',
            get_string('gateway_mollie_partner_id', 'enrol_coursepayment'), '', '',
            PARAM_INT));
        $settings->add(new admin_setting_configtext('enrol_coursepayment/gateway_mollie_profile_key',
            get_string('gateway_mollie_profile_key', 'enrol_coursepayment'), '', '',
            PARAM_TEXT));
        $settings->add(new admin_setting_configtext('enrol_coursepayment/gateway_mollie_app_secret',
            get_string('gateway_mollie_app_secret', 'enrol_coursepayment'), '', '',
            PARAM_TEXT));
    }

    if (!empty($config->gateway_mollie_apikey)) {
        try {
            $gateway = new enrol_coursepayment_mollie();
            $methods = $gateway->get_enabled_modes();
            $settings->add(new admin_setting_heading('enrol_coursepayment_methods', '', $methods));
        } catch (Exception $exc) {
            $settings->add(new admin_setting_heading('enrol_coursepayment_warning', '',
                '<div style="color:red">' . $exc->getMessage() . '</div>'));
        }
    }
}
