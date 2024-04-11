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
 * Accept Mollie connect
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 16/01/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

require_once(__DIR__ . '/../../../config.php');
defined('MOODLE_INTERNAL') || die;
require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/enrol/coursepayment/view/accept.php');

set_config('mollie_connect_accepted', 1, 'enrol_coursepayment');

redirect(new moodle_url('/admin/settings.php', [
    'section' => 'enrolsettingscoursepayment',
    'gateway' => 's_enrol_coursepayment_tabs',
]));
