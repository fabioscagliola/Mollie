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
 * Multi account
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2017 MFreak.nl
 * @author    Luuk Verhoeven
 **/

require_once(__DIR__ . '/../../../config.php');
require_login();

$context = context_system::instance();

if (!has_capability('enrol/coursepayment:config', $context)) {
    throw new moodle_exception('error:capability_config', 'enrol_coursepayment');
}
$PAGE->navbar->add(get_string('pluginname', 'enrol_coursepayment'),
    new moodle_url('/admin/settings.php', ['section' => 'enrolsettingscoursepayment']));
$PAGE->navbar->add(get_string('enrol_coursepayment_multi_account', 'enrol_coursepayment'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('enrol_coursepayment_multi_account', 'enrol_coursepayment'));

$action = optional_param('action', false, PARAM_ALPHA);
$id = optional_param('id', false, PARAM_INT);
$PAGE->set_url('/enrol/coursepayment/view/multi_account.php', [
    'action' => $action,
    'id' => $id,
]);

switch ($action) {
    case 'pdf':

        $pdf = $DB->get_record('coursepayment_templates', ['name' => $id], 'id', MUST_EXIST);
        redirect(new moodle_url('/enrol/coursepayment/view/invoice_edit.php', ['tid' => $pdf->id]));
        break;

    case 'delete':
        $DB->delete_records('coursepayment_multiaccount', ['id' => $id]);

        redirect(new moodle_url('/admin/settings.php', [
            'section' => 'enrolsettingscoursepayment',
            's_enrol_coursepayment_tabs' => 'multiaccount',
        ]));
        break;
    case 'new':
    case 'edit':

        $form = new \enrol_coursepayment\form\multiaccount($PAGE->url);

        if ($form->is_cancelled()) {
            redirect(new moodle_url('/admin/settings.php', [
                'section' => 'enrolsettingscoursepayment',
                's_enrol_coursepayment_tabs' => 'multiaccount',
            ]));
        }

        if ($action == 'edit') {
            // Get multi-account.
            $multiaccount = $DB->get_record('coursepayment_multiaccount', ['id' => $id], '*', MUST_EXIST);
            $form->set_data($multiaccount);
        }

        if (($data = $form->get_data()) != false) {

            if ($id > 0) {
                $data->id = $multiaccount->id;
                $DB->update_record('coursepayment_multiaccount', $data);

            } else {
                $total = $DB->count_records('coursepayment_multiaccount');

                $data->is_default = empty($total) ? 1 : 0;
                $data->added_on = time();
                $id = $DB->insert_record('coursepayment_multiaccount', $data);

                // Add a default invoice template.
                \enrol_coursepayment\invoice\template::install_default_template($id);
            }

            redirect(new moodle_url('/admin/settings.php', [
                'section' => 'enrolsettingscoursepayment',
                's_enrol_coursepayment_tabs' => 'multiaccount',
            ]));
        }

        echo $OUTPUT->header();
        echo $form->render();
        echo $OUTPUT->footer();
        break;
}
