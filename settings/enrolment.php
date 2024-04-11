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
 * Enrolment global setting page.
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

if (!during_initial_install()) {
    $options = get_default_enrol_roles(context_system::instance());
    $student = get_archetype_roles('student');
    $student = reset($student);
    $settings->add(new admin_setting_configselect('enrol_coursepayment/roleid',
        get_string('defaultrole', 'enrol_coursepayment'),
        get_string('defaultrole_desc', 'enrol_coursepayment'), $student->id, $options));
}
$settings->add(new admin_setting_configduration('enrol_coursepayment/enrolperiod',
    get_string('enrolperiod', 'enrol_coursepayment'),
    get_string('enrolperiod_desc', 'enrol_coursepayment'), 0));
$options = [
    ENROL_EXT_REMOVED_KEEP => get_string('extremovedkeep', 'enrol'),
    ENROL_EXT_REMOVED_SUSPENDNOROLES => get_string('extremovedsuspendnoroles', 'enrol'),
    ENROL_EXT_REMOVED_UNENROL => get_string('extremovedunenrol', 'enrol'),
];
$settings->add(new admin_setting_configselect('enrol_coursepayment/expiredaction',
    get_string('expiredaction', 'enrol_coursepayment'),
    get_string('expiredaction_help', 'enrol_coursepayment'),
    ENROL_EXT_REMOVED_SUSPENDNOROLES, $options));



