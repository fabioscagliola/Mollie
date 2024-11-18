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
 * This is the external API for this tool.
 *
 * this part is copied from "enrol_coursepayment" - Mark Nelson <markn@moodle.com>
 * Thanks for allowing us to use it.
 *
 * This file is modified not compatible with the original.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 */

namespace enrol_coursepayment;

use enrol_coursepayment\invoice\element_factory;
use enrol_coursepayment\invoice\template;
use external_function_parameters;
use external_value;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once("$CFG->libdir/externallib.php");

/**
 * This is the external API for this tool.
 *
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends \external_api {

    /**
     * Returns the save_element() parameters.
     *
     * @return external_function_parameters
     */
    public static function save_element_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'templateid' => new external_value(PARAM_INT, 'The template id'),
                'elementid' => new external_value(PARAM_INT, 'The element id'),
                'values' => new \external_multiple_structure(
                    new \external_single_structure(
                        [
                            'name' => new external_value(PARAM_ALPHANUMEXT, 'The field to update'),
                            'value' => new external_value(PARAM_RAW, 'The value of the field'),
                        ]
                    )
                ),
            ]
        );
    }

    /**
     * Handles saving element data.
     *
     * @param int $templateid The template id.
     * @param int $elementid  The element id.
     * @param array $values   The values to save
     *
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     */
    public static function save_element(int $templateid, int $elementid, array $values): bool {
        global $DB;

        $params = [
            'templateid' => $templateid,
            'elementid' => $elementid,
            'values' => $values,
        ];
        self::validate_parameters(self::save_element_parameters(), $params);

        $template = $DB->get_record('coursepayment_templates', ['id' => $templateid], '*', MUST_EXIST);
        $element = $DB->get_record('coursepayment_elements', ['id' => $elementid], '*', MUST_EXIST);

        // Set the template.
        $template = new template($template);

        // Perform checks.
        self::validate_context(\context_system::instance());

        // Make sure the user has the required capabilities.
        $template->require_manage();

        // Set the values we are going to save.
        $data = new \stdClass();
        $data->id = $element->id;
        $data->name = $element->name;
        foreach ($values as $value) {
            $field = $value['name'];
            $data->$field = $value['value'];
        }

        // Get an instance of the element class.
        if ($e = element_factory::get_element_instance($element)) {
            return (bool) $e->save_form_elements($data);
        }

        return false;
    }

    /**
     * Returns the save_element result value.
     *
     * @return external_value
     */
    public static function save_element_returns(): external_value {
        return new external_value(PARAM_BOOL, 'True if successful, false otherwise');
    }

    /**
     * Returns get_element() parameters.
     *
     * @return external_function_parameters
     */
    public static function get_element_html_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'templateid' => new external_value(PARAM_INT, 'The template id'),
                'elementid' => new external_value(PARAM_INT, 'The element id'),
            ]
        );
    }

    /**
     * Handles return the element's HTML.
     *
     * @param int $templateid The template id
     * @param int $elementid  The element id.
     *
     * @return string
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \restricted_context_exception
     * @throws \coding_exception
     */
    public static function get_element_html(int $templateid, int $elementid): string {
        global $DB;

        $params = [
            'templateid' => $templateid,
            'elementid' => $elementid,
        ];
        self::validate_parameters(self::get_element_html_parameters(), $params);
        $element = $DB->get_record('coursepayment_elements', ['id' => $elementid], '*', MUST_EXIST);

        // Perform checks.
        self::validate_context(\context_system::instance());

        // Get an instance of the element class.
        if ($e = element_factory::get_element_instance($element)) {
            return $e->render_html();
        }

        return '';
    }

    /**
     * Returns the get_element result value.
     *
     * @return external_value
     */
    public static function get_element_html_returns(): external_value {
        return new external_value(PARAM_RAW, 'The HTML');
    }

}
