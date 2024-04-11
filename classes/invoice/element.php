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
 * The base element class
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

/**
 * Class element
 *
 * All enrol_coursepayment invoice element plugins are based on this class.
 */
abstract class element {

    /**
     * @var \stdClass $element The data for the element we are adding - do not use, kept for legacy reasons.
     */
    protected \stdClass $element;

    /**
     * @var int|null The id.
     */
    protected $id;

    /**
     * @var int|null The page id.
     */
    protected $pageid;

    /**
     * @var string The name.
     */
    protected string $name;

    /**
     * @var mixed|null The data.
     */
    protected $data;

    /**
     * @var string|null The font name.
     */
    protected $font;

    /**
     * @var int|null The font size.
     */
    protected $fontsize;

    /**
     * @var string|null The font colour.
     */
    protected $colour;

    /**
     * @var int|null The position x.
     */
    protected $posx;

    /**
     * @var int|null The position y.
     */
    protected $posy;

    /**
     * @var int|null The width.
     */
    protected $width;

    /**
     * @var int|null The refpoint.
     */
    protected $refpoint;

    /**
     * @var bool $showposxy Show position XY form elements?
     */
    protected bool $showposxy;

    /**
     * Constructor.
     *
     * @param \stdClass $element the element data
     *
     */
    public function __construct(\stdClass $element) {

        // Keeping this for legacy reasons, so we do not break third-party elements.
        $this->element = clone($element);

        $this->id = $element->id;
        $this->pageid = $element->pageid;
        $this->name = $element->name;
        $this->data = $element->data;
        $this->font = $element->font;
        $this->fontsize = $element->fontsize;
        $this->colour = $element->colour;
        $this->posx = $element->posx ?? 0;
        $this->posy = $element->posy ?? 0;
        $this->width = $element->width;
        $this->refpoint = $element->refpoint;
        $this->showposxy = true;
    }

    /**
     * Returns the id.
     *
     * @return int
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Returns the page id.
     *
     * @return int
     */
    public function get_pageid(): int {
        return $this->pageid;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Returns the data.
     *
     * @return mixed
     */
    public function get_data() {
        return $this->data;
    }

    /**
     * Returns the font name.
     *
     * @return string
     */
    public function get_font(): string {
        return $this->font;
    }

    /**
     * Returns the font size.
     *
     * @return int
     */
    public function get_fontsize(): int {
        return $this->fontsize;
    }

    /**
     * Returns the font colour.
     *
     * @return string
     */
    public function get_colour(): string {
        return $this->colour;
    }

    /**
     * Returns the position x.
     *
     * @return int
     */
    public function get_posx(): int {
        return $this->posx;
    }

    /**
     * Returns the position y.
     *
     * @return int
     */
    public function get_posy(): int {
        return $this->posy;
    }

    /**
     * Returns the width.
     *
     * @return int
     */
    public function get_width(): int {
        return $this->width;
    }

    /**
     * Returns the refpoint.
     *
     * @return int
     */
    public function get_refpoint(): int {
        return $this->refpoint;
    }

    /**
     * This function renders the form elements when adding a customcert element.
     * Can be overridden if more functionality is needed.
     *
     * @param edit_element_form $mform the edit_form instance.
     *
     * @return void
     * @throws \coding_exception
     */
    public function render_form_elements($mform): void {
        // Render the common elements.
        element_helper::render_form_element_font($mform);
        element_helper::render_form_element_colour($mform);
        if ($this->showposxy) {
            element_helper::render_form_element_position($mform);
        }
        element_helper::render_form_element_width($mform);
    }

    /**
     * Sets the data on the form when editing an element.
     * Can be overridden if more functionality is needed.
     *
     * @param edit_element_form $mform the edit_form instance
     *
     * @return void
     */
    public function definition_after_data($mform): void {
        // Loop through the properties of the element and set the values
        // of the corresponding form element, if it exists.
        $properties = [
            'name' => $this->name,
            'font' => $this->font,
            'fontsize' => $this->fontsize,
            'colour' => $this->colour,
            'posx' => $this->posx,
            'posy' => $this->posy,
            'width' => $this->width,
            'refpoint' => $this->refpoint,
        ];
        foreach ($properties as $property => $value) {
            if (!is_null($value) && $mform->elementExists($property)) {
                $element = $mform->getElement($property);
                $element->setValue($value);
            }
        }
    }

    /**
     * Performs validation on the element values.
     * Can be overridden if more functionality is needed.
     *
     * @param array $data  the submitted data
     * @param array $files the submitted files
     *
     * @return array the validation errors
     * @throws \coding_exception
     */
    public function validate_form_elements($data, $files): array {
        // Array to return the errors.
        $errors = [];

        // Common validation methods.
        $errors += element_helper::validate_form_element_colour($data);
        if ($this->showposxy) {
            $errors += element_helper::validate_form_element_position($data);
        }
        $errors += element_helper::validate_form_element_width($data);

        return $errors;
    }

    /**
     * Handles saving the form elements created by this element.
     * Can be overridden if more functionality is needed.
     *
     * @param \stdClass $data the form data
     *
     * @return bool true of success, false otherwise.
     * @throws \dml_exception
     */
    public function save_form_elements($data): bool {
        global $DB;

        // Get the data from the form.
        $element = new \stdClass();
        $element->name = $data->name;
        $element->data = $this->save_unique_data($data);
        $element->font = (isset($data->font)) ? $data->font : null;
        $element->fontsize = (isset($data->fontsize)) ? $data->fontsize : null;
        $element->colour = (isset($data->colour)) ? $data->colour : null;
        if ($this->showposxy) {
            $element->posx = (isset($data->posx)) ? $data->posx : 0;
            $element->posy = (isset($data->posy)) ? $data->posy : 0;
        }
        $element->width = (isset($data->width)) ? $data->width : null;
        $element->refpoint = (isset($data->refpoint)) ? $data->refpoint : null;
        $element->timemodified = time();

        // Check if we are updating, or inserting a new element.
        if (!empty($this->id)) { // Must be updating a record in the database.
            $element->id = $this->id;

            return $DB->update_record('coursepayment_elements', $element);
        } else { // Must be adding a new one.
            $element->element = $data->element;
            $element->pageid = $data->pageid;
            $element->sequence = element_helper::get_element_sequence($element->pageid);
            $element->timecreated = time();

            return $DB->insert_record('coursepayment_elements', $element, false);
        }
    }

    /**
     * This will handle how form data will be saved into the data column in the
     * coursepayment_elements table.
     * Can be overridden if more functionality is needed.
     *
     * @param \stdClass $data the form data
     *
     * @return string the unique data to save
     */
    public function save_unique_data($data): string {
        return '';
    }

    /**
     * This handles copying data from another element of the same type.
     * Can be overridden if more functionality is needed.
     *
     * @param \stdClass $data the form data
     *
     * @return bool returns true if the data was copied successfully, false otherwise
     */
    public function copy_element($data): bool {
        return true;
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * Must be overridden.
     *
     * @param \pdf $pdf       the pdf object
     * @param bool $preview   true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     * @param array $data     extra data provided by coursepayment
     */
    abstract public function render($pdf, $preview, $user, array $data = []);

    /**
     * Render the element in html.
     *
     * Must be overridden.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     *
     * @return string the html
     */
    abstract public function render_html(): string;

    /**
     * Handles deleting any data this element may have introduced.
     * Can be overridden if more functionality is needed.
     *
     * @return bool success return true if deletion success, false otherwise
     * @throws \dml_exception
     */
    public function delete(): bool {
        global $DB;

        return $DB->delete_records('coursepayment_elements', ['id' => $this->id]);
    }

    /**
     * This function is responsible for handling the restoration process of the element.
     *
     * For example, the function may save data that is related to another course module, this
     * data will need to be updated if we are restoring the course as the course module id will
     * be different in the new course.
     *
     * @param \restore_coursepayment_activity_task $restore
     *
     * @return void
     */
    public function after_restore($restore): void {

    }

    /**
     * Magic getter for read only access.
     *
     * @param string $name
     *
     * @return string|void
     */
    public function __get(string $name) {
        debugging('Please call the appropriate get_* function instead of relying on magic getters', DEBUG_DEVELOPER);
        if (property_exists($this->element, $name)) {
            return $this->element->$name;
        }
    }

}
