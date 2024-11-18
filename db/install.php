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
 * Execute on first time install plugin.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 12-11-2018 MFreak.nl
 * @author    Luuk Verhoeven
 **/

/**
 * Execute during install.
 *
 * @throws dml_exception
 */
function xmldb_enrol_coursepayment_install(): void {
    global $DB;

    // Requires Mollie connect for new installations.
    set_config('mollieconnect', 1, 'enrol_coursepayment');

    // Install a default template.
    // Check if there is a template.
    if ($DB->record_exists('coursepayment_templates', ['id' => 1])) {
        return;
    }

    \enrol_coursepayment\invoice\template::install_default_template();
}
