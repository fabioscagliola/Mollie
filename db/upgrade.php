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
 * Upgrade older versions to support new features.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */

/**
 * Execute during upgrade.
 *
 * @param int $oldversion
 *
 * @return bool
 * @throws ddl_exception
 * @throws ddl_field_missing_exception
 * @throws ddl_table_missing_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 * @throws dml_exception
 */
function xmldb_enrol_coursepayment_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    // Add discount code feature.
    if ($oldversion < 2015051500) {

        // Define field discountdata to be added to enrol_coursepayment.
        $table = new xmldb_table('enrol_coursepayment');
        $field = new xmldb_field('discountdata', XMLDB_TYPE_TEXT, null, null, null, null, null, 'addedon');

        // Conditionally launch add field discountdata.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('enrol_coursepayment_discount');

        // Adding fields to table enrol_coursepayment_discount.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('code', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, null, null);
        $table->add_field('start_time', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, null, null);
        $table->add_field('end_time', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, null, null);
        $table->add_field('percentage', XMLDB_TYPE_NUMBER, '8, 5', null, XMLDB_NOTNULL, null, '0.00000');
        $table->add_field('amount', XMLDB_TYPE_NUMBER, '10, 2', null, XMLDB_NOTNULL, null, '0.00');
        $table->add_field('created_by', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for enrol_coursepayment_discount.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Coursepayment savepoint reached.
        upgrade_plugin_savepoint(true, 2015051500, 'enrol', 'coursepayment');
    }

    // Add support for invoice numbers.
    if ($oldversion < 2015061201) {

        // Define field invoice_number to be added to enrol_coursepayment.
        $table = new xmldb_table('enrol_coursepayment');
        $field = new xmldb_field('invoice_number', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'cost');

        // Conditionally launch add field invoice_number.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $molie = new enrol_coursepayment_mollie();
        $molie->upgrade_invoice_numbers();
        upgrade_plugin_savepoint(true, 2015061201, 'enrol', 'coursepayment');
    }

    // Add vat support.
    if ($oldversion < 2015061203) {

        // Define field invoice_number to be added to enrol_coursepayment.
        $table = new xmldb_table('enrol_coursepayment');
        $field = new xmldb_field('vatpercentage', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '21', 'cost');

        // Conditionally launch add field invoice_number.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Coursepayment savepoint reached.
        upgrade_plugin_savepoint(true, 2015061203, 'enrol', 'coursepayment');
    }

    // Add activity condition support.
    if ($oldversion < 2016111201) {
        // Define field cmid to be added to enrol_coursepayment.
        $table = new xmldb_table('enrol_coursepayment');
        $field = new xmldb_field('cmid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0', 'invoice_number');

        // Conditionally launch add field cmid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('is_activity', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'cmid');

        // Conditionally launch add field is_activity.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field cmid to be added to enrol_coursepayment_discount.
        $table = new xmldb_table('enrol_coursepayment_discount');
        $field = new xmldb_field('cmid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0', 'amount');

        // Conditionally launch add field cmid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Coursepayment savepoint reached.
        upgrade_plugin_savepoint(true, 2016111201, 'enrol', 'coursepayment');
    }

    if ($oldversion < 2017031002) {

        // Define field section to be added to enrol_coursepayment.
        $table = new xmldb_table('enrol_coursepayment');
        $field = new xmldb_field('section', XMLDB_TYPE_INTEGER, '3', null, null, null, '-10', 'is_activity');

        // Conditionally launch add field section.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Coursepayment savepoint reached.
        upgrade_plugin_savepoint(true, 2017031002, 'enrol', 'coursepayment');
    }

    if ($oldversion < 2017082400) {

        // Define table coursepayment_multiaccount to be created.
        $table = new xmldb_table('coursepayment_multiaccount');

        // Adding fields to table coursepayment_multiaccount.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('profile_value', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
        $table->add_field('is_default', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, null, null);
        $table->add_field('company_name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('address', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('place', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('zipcode', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('kvk', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('btw', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gateway_mollie_apikey', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gateway_mollie_profile_key', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gateway_mollie_partner_id', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gateway_mollie_app_secret', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gateway_mollie_debug', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('gateway_mollie_sandbox', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('added_on', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for coursepayment_multiaccount.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Coursepayment savepoint reached.
        upgrade_plugin_savepoint(true, 2017082400, 'enrol', 'coursepayment');
    }

    if ($oldversion < 2017091800) {

        // Define field gateway_mollie_profile_key to be dropped from coursepayment_multiaccount.
        $table = new xmldb_table('coursepayment_multiaccount');
        $field = new xmldb_field('gateway_mollie_app_secret');

        // Conditionally launch drop field gateway_mollie_profile_key.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Coursepayment savepoint reached.
        upgrade_plugin_savepoint(true, 2017091800, 'enrol', 'coursepayment');
    }

    // Add new tables for customizing invoice.
    if ($oldversion < 2019040300) {

        // Define table coursepayment_elements to be created.
        $table = new xmldb_table('coursepayment_elements');

        // Adding fields to table coursepayment_elements.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('pageid', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('element', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('data', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('font', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('fontsize', XMLDB_TYPE_INTEGER, '18', null, null, null, null);
        $table->add_field('colour', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('posx', XMLDB_TYPE_INTEGER, '18', null, null, null, null);
        $table->add_field('posy', XMLDB_TYPE_INTEGER, '18', null, null, null, null);
        $table->add_field('width', XMLDB_TYPE_INTEGER, '18', null, null, null, null);
        $table->add_field('refpoint', XMLDB_TYPE_INTEGER, '4', null, null, null, null);
        $table->add_field('sequence', XMLDB_TYPE_INTEGER, '18', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table coursepayment_elements.
        $table->add_index('mdl_custelem_pag_ix', XMLDB_INDEX_NOTUNIQUE, ['pageid']);

        // Conditionally launch create table for coursepayment_elements.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table coursepayment_templates to be created.
        $table = new xmldb_table('coursepayment_templates');

        // Adding fields to table coursepayment_templates.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table coursepayment_templates.
        $table->add_index('mdl_custtemp_con_ix', XMLDB_INDEX_NOTUNIQUE, ['contextid']);

        // Conditionally launch create table for coursepayment_templates.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table coursepayment_pages to be created.
        $table = new xmldb_table('coursepayment_pages');

        // Adding fields to table coursepayment_pages.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('width', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('height', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('leftmargin', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('rightmargin', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('sequence', XMLDB_TYPE_INTEGER, '18', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table coursepayment_pages.
        $table->add_index('mdl_custpage_tem_ix', XMLDB_INDEX_NOTUNIQUE, ['templateid']);

        // Conditionally launch create table for coursepayment_pages.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Install a default template.
        \enrol_coursepayment\invoice\template::install_default_template();

        // Coursepayment savepoint reached.
        upgrade_plugin_savepoint(true, 2019040300, 'enrol', 'coursepayment');
    }

    return true;
}
