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
 * Coursepayment enrolments plugin settings and presets.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 **/

defined('MOODLE_INTERNAL') || die();

//global $ADMIN;

if ($ADMIN->fulltree) {
    $install = $PAGE->url->get_path() === '/admin/upgradesettings.php' ? true : false;
    $message = optional_param('message', false, PARAM_TEXT);
    $config = get_config('enrol_coursepayment');

    // Add mollie settings to the plugin https://www.mollie.com.
    $yesno = [
        0 => get_string('no'),
        1 => get_string('yes'),
    ];

    // Add some more logic.
    $tabs = new \enrol_coursepayment\adminsetting\tabs('enrol_coursepayment/tabs', $settings->name, 'invoicedetails');
    $tabs->addtab('invoicedetails', get_string('settings:tab_invoicedetails', 'enrol_coursepayment'));
    $tabs->addtab('mail', get_string('settings:tab_mail', 'enrol_coursepayment'));
    $tabs->addtab('gateway', get_string('settings:tab_gateway', 'enrol_coursepayment'));
    $tabs->addtab('enrolment', get_string('settings:tab_enrolment', 'enrol_coursepayment'));
    if (!empty($config->multi_account)) {
        $tabs->addtab('multiaccount', get_string('settings:tab_multiaccount', 'enrol_coursepayment'));
    }
    $tabs->addtab('advanced', get_string('settings:tab_advanced', 'enrol_coursepayment'));
    $settings->add($tabs);

    $tab = $tabs->get_setting();

    // Check if there is a message.
    if (!empty($message)) {
        $settings->add(new admin_setting_heading('enrol_coursepayment_message', '',
            html_writer::div(get_string('message:' . $message, 'enrol_coursepayment'), 'alert alert-success')));
    }

    // Display the correct tab.
    if (empty($install) && (empty($tab) || $tab === 'invoicedetails')) {

        // Invoice settings.
        include('settings/invoicedetails.php');

    } else if ($tab === 'enrolment') {

        // Enrolment settings.
        include('settings/enrolment.php');

    } else if ($tab === 'mail') {

        // E-mail settings.
        include('settings/mail.php');

    } else if ($tab === 'multiaccount') {

        // E-mail settings.
        include('settings/multiaccount.php');

    } else if ($tab === 'gateway') {

        // Gateway settings.
        include('settings/gateway.php');

    } else if ($tab === 'advanced') {

        // Advanced settings.
        include('settings/advanced.php');
    }

    // Install make sure all values get set while installing.
    if (!empty($install)) {

        // Invoice settings.
        include('settings/invoicedetails.php');
        // Enrolment settings.
        include('settings/enrolment.php');
        // E-mail settings.
        include('settings/mail.php');
        // E-mail settings.
        include('settings/multiaccount.php');
        // Gateway settings.
        include('settings/gateway.php');
        // Advanced settings.
        include('settings/advanced.php');
    }
}
