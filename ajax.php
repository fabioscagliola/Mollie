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
 * Ajax api calls
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 **/

// These definitions should stay on top, before MOODLE_INTERNAL check.
// @codingStandardsIgnoreStart
if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}
define('NO_DEBUG_DISPLAY', true);
// @codingStandardsIgnoreEnd

require('../../config.php');
defined('MOODLE_INTERNAL') || die;

$PAGE->set_url('/enrol/coursepayment/ajax.php');

require_login(get_site(), true, null, true, true);

// Params.
$sesskey = required_param('sesskey', PARAM_RAW);
$courseid = required_param('courseid', PARAM_INT);
$action = required_param('action', PARAM_TEXT);
$data = required_param('data', PARAM_RAW);

// Get the course.
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

// Get plugin config.
$config = get_config('enrol_coursepayment');

// Default return array.
$array = [
    'error' => '',
    'status' => false,
];

if (!confirm_sesskey($sesskey)) {
    $array['error'] = get_string('failed:sesskey', 'enrol_coursepayment');
}
if (empty($array['error'])) {

    switch ($action) {

        // Validate a discount code.
        case 'discountcode':
            $discountinstance = new enrol_coursepayment_discountcode($data, $courseid);
            $row = $discountinstance->get_discountcode();

            if ($row) {

                $array['amount'] = $row->amount;
                $array['percentage'] = $row->percentage;
                $array['status'] = true;

                unset($array['error']);
            } else {
                $array['error'] = $discountinstance->get_last_error();
            }
            break;

        case 'update_invoice_element':

            $tid = required_param('tid', PARAM_INT);

            // Make sure the template exists.
            $template = $DB->get_record('coursepayment_templates', ['id' => $tid], '*', MUST_EXIST);

            // Set the template.
            $template = new \enrol_coursepayment\invoice\template($template);

            // Perform checks.
            require_login();

            // Make sure the user has the required capabilities.
            $template->require_manage();

            // Loop through the data.
            $data = json_decode($data);
            if (!empty($data)) {
                foreach ($data as $value) {
                    $element = new stdClass();
                    $element->id = $value->id;
                    $element->posx = $value->posx;
                    $element->posy = $value->posy;
                    $DB->update_record('coursepayment_elements', $element);
                }
                $array['status'] = true;
            }
            break;
    }
}

echo json_encode($array);
