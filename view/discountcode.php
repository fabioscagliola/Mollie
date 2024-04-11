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
 * Overview CRUD discount code
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */

require_once(__DIR__ . '/../../../config.php');
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');
require_login();

$context = context_system::instance();

if (!has_capability('enrol/coursepayment:config', $context)) {
    $errormessage = get_string('error:capability_config', 'enrol_coursepayment');
    throw new required_capability_exception($context, 'enrol/coursepayment:config', $errormessage, '');
}

// Set navbar.
$PAGE->navbar->add(get_string('pluginname', 'enrol_coursepayment'),
    new moodle_url('/admin/settings.php', ['section' => 'enrolsettingscoursepayment']));
$PAGE->navbar->add(get_string('enrol_coursepayment_discount', 'enrol_coursepayment'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('enrol_coursepayment_discount', 'enrol_coursepayment'));

$action = optional_param('action', false, PARAM_ALPHA);
$id = optional_param('id', false, PARAM_INT);
$PAGE->requires->js('/enrol/coursepayment/js/helper.js');
$PAGE->set_url('/enrol/coursepayment/view/discountcode.php', [
    'action' => $action,
    'id' => $id,
]);

switch ($action) {

    case 'delete':
        $DB->delete_records('enrol_coursepayment_discount', ['id' => $id]);
        redirect(new moodle_url('/enrol/coursepayment/view/discountcode.php'));
        break;

    case 'add':
    case 'edit':
        $form = new \enrol_coursepayment\form\discountcode($PAGE->url);
        if ($action == 'edit') {
            // Load the item.
            $row = $DB->get_record('enrol_coursepayment_discount', ['id' => $id], '*', MUST_EXIST);
            $form->set_data($row);
        }

        // Cancel form.
        if ($form->is_cancelled()) {
            redirect(new moodle_url('/enrol/coursepayment/view/discountcode.php'));
        }

        if (($data = $form->get_data()) != false) {

            $data->created_by = $USER->id;

            // Save to the db.
            if ($action == 'edit') {
                $data->id = $row->id;
                $DB->update_record('enrol_coursepayment_discount', $data);
            } else {
                // Make sure the code is unique.
                $item = $DB->get_record('enrol_coursepayment_discount', ['code' => $data->code],
                    '*', IGNORE_MULTIPLE);
                if ($item) {
                    // This is bad we already have this code we need to throw a error.
                    throw new moodle_exception('error:code_not_unique', 'enrol_coursepayment');
                }

                $DB->insert_record('enrol_coursepayment_discount', $data);
            }
            redirect(new moodle_url('/enrol/coursepayment/view/discountcode.php'));
        }

        echo $OUTPUT->header();
        echo $form->render();
        echo $OUTPUT->footer();
        break;

    default:
        echo $OUTPUT->header();

        // Build the table.
        $table = new \enrol_coursepayment\table\discountcode('enrol_coursepayment-discounttable');
        $newurl = new moodle_url($PAGE->url, ['action' => 'add']);
        echo $OUTPUT->render(new single_button($newurl, get_string('new:discountcode',
            'enrol_coursepayment')));
        echo '<hr/>';

        $dbfields = 'id, code, courseid, start_time, end_time, percentage, amount';
        $sqlconditions = '1=1';
        $sqlparams = [];

        $table->set_sql($dbfields, '{enrol_coursepayment_discount}', $sqlconditions, $sqlparams);
        $table->set_count_sql("SELECT COUNT(*) FROM {enrol_coursepayment_discount} WHERE $sqlconditions", $sqlparams);
        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('class', 'admintable generaltable');
        $table->initialbars(true); // Always initial bars.
        $table->define_columns([
            'code',
            'courseid',
            'start_time',
            'end_time',
            'amount',
            'action',
        ]);

        $table->define_headers([
            get_string('th:code', 'enrol_coursepayment'),
            get_string('th:courseid', 'enrol_coursepayment'),
            get_string('th:start_time', 'enrol_coursepayment'),
            get_string('th:end_time', 'enrol_coursepayment'),
            get_string('th:amount', 'enrol_coursepayment'),
            get_string('th:action', 'enrol_coursepayment'),
        ]);

        $table->sortable(true, 'courseid', SORT_ASC);

        $table->define_baseurl($PAGE->url);
        $table->collapsible(false);
        $table->out(50, true);

        echo $OUTPUT->footer();
}
