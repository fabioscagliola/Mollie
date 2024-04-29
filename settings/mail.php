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
 * Mail global setting page.
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

$settings->add(new admin_setting_heading('enrol_coursepayment_welcomemail',
    get_string('welcomemail', 'enrol_coursepayment'), ''));

$settings->add(new admin_setting_configcheckbox('enrol_coursepayment/mailstudents',
    get_string('mailstudents', 'enrol_coursepayment'),
    enrol_coursepayment_helper::strip_welcome_to_course_text(
        get_string('welcometocoursetext', '')
    ),
    0),
);

$settings->add(new admin_setting_configcheckbox('enrol_coursepayment/mailteachers',
    get_string('mailteachers', 'enrol_coursepayment'),
    get_string('enrolmentnewuser', 'enrol'), 0));

$settings->add(new admin_setting_configcheckbox('enrol_coursepayment/mailadmins',
    get_string('mailadmins', 'enrol_coursepayment'),
    get_string('enrolmentnewuser', 'enrol'), 0));

$settings->add(new admin_setting_heading('enrol_coursepayment_invoicemail',
    get_string('invoicemail', 'enrol_coursepayment'), ''));

$settings->add(new admin_setting_configcheckbox('enrol_coursepayment/mailstudents_invoice',
    get_string('mailstudents', 'enrol_coursepayment'), '', 1));

$settings->add(new admin_setting_configcheckbox('enrol_coursepayment/mailteachers_invoice',
    get_string('mailteachers', 'enrol_coursepayment'), '', 0));

$settings->add(new admin_setting_configcheckbox('enrol_coursepayment/mailadmins_invoice',
    get_string('mailadmins', 'enrol_coursepayment'), '', 1));

$settings->add(new admin_setting_configtext('enrol_coursepayment/custom_mails_invoice',
    get_string('custommails', 'enrol_coursepayment'),
    get_string('custommails_desc', 'enrol_coursepayment'), '', PARAM_TEXT));

$options = [];
for ($i = 0; $i < 24; $i++) {
    $options[$i] = $i;
}
$settings->add(new admin_setting_configselect('enrol_coursepayment/expirynotifyhour',
    get_string('expirynotifyhour', 'core_enrol'), '', 6, $options));
$options = [
    0 => get_string('no'),
    1 => get_string('expirynotifyenroller', 'core_enrol'),
    2 => get_string('expirynotifyall', 'core_enrol'),
];
$settings->add(new admin_setting_configselect('enrol_coursepayment/expirynotify',
    get_string('expirynotify', 'core_enrol'),
    get_string('expirynotify_help', 'core_enrol'), 0, $options));

$settings->add(new admin_setting_configduration('enrol_coursepayment/expirythreshold',
    get_string('expirythreshold', 'core_enrol'),
    get_string('expirythreshold_help', 'core_enrol'), 86400, 86400));
