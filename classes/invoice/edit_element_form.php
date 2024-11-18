<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * This file contains the form for handling editing a coursepayment invoice element.
 *
 * this part is copied from "mod_customcert" - Mark Nelson <markn@moodle.com>
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

namespace enrol_coursepayment\invoice;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

global $CFG;
require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/enrol/coursepayment/classes/invoice/colourpicker.php');

\MoodleQuickForm::registerElementType('coursepayment_colourpicker',
    $CFG->dirroot . '/enrol/coursepayment/classes/invoice/colourpicker.php', 'enrol_coursepayment\invoice\colourpicker');

/**
 *  The form for handling editing a coursepayment invoice element.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 */
class edit_element_form extends \moodleform {

    /**
     * @var element The element object.
     */
    protected mixed $element;

    /**
     * Form definition.
     *
     * @return void
     * @throws \coding_exception
     */
    public function definition(): void {
        $mform =& $this->_form;

        $mform->updateAttributes(['id' => 'editelementform']);

        $element = $this->_customdata['element'];

        // Add the field for the name of the element, this is required for all elements.
        $mform->addElement('text', 'name', get_string('elementname', 'enrol_coursepayment'), 'maxlength="255"');
        $mform->setType('name', PARAM_TEXT);
        $mform->setDefault('name', get_string('invoice_element_' . $element->element, 'enrol_coursepayment'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('name', 'elementname', 'enrol_coursepayment');

        $this->element = element_factory::get_element_instance($element);
        $this->element->render_form_elements($mform);

        $this->add_action_buttons(true);
    }

    /**
     * Fill in the current page data for this coursepayment invoice.
     *
     * @return void
     */
    public function definition_after_data(): void {
        $this->element->definition_after_data($this->_form);
    }

    /**
     * Validation.
     *
     * @param array $data
     * @param array $files
     *
     * @return array the errors that were found
     * @throws \coding_exception
     */
    public function validation($data, $files): array {
        $errors = [];

        if (\core_text::strlen($data['name']) > 255) {
            $errors['name'] = get_string('nametoolong', 'enrol_coursepayment');
        }

        $errors += $this->element->validate_form_elements($data, $files);

        return $errors;
    }

}
