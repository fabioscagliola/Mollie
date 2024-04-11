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
 * This file contains the coursepayment element bgimage's core interaction API.
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

namespace enrol_coursepayment\invoice\element\bgimage;

/**
 * The element background image's core interaction API.
 *
 * @package    enrol_coursepayment
 * @copyright  2018 MFreak.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \enrol_coursepayment\invoice\element\image\element {

    /**
     * This function renders the form elements when adding a customcert element.
     *
     * @param \enrol_coursepayment\invoice\edit_element_form $mform the edit_form instance
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function render_form_elements($mform): void {
        $mform->addElement('select', 'fileid', get_string('image', 'enrol_coursepayment'), self::get_images());
        $mform->addElement('filemanager', 'coursepaymentimage', get_string('uploadimage', 'enrol_coursepayment'), '',
            $this->filemanageroptions);
    }

    /**
     * Performs validation on the element values.
     *
     * @param array $data  the submitted data
     * @param array $files the submitted files
     *
     * @return array the validation errors
     */
    public function validate_form_elements($data, $files): array {
        // Array to return the errors.
        return [];
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf       the pdf object
     * @param bool $preview   true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     * @param array $data
     *
     * @return void
     */
    public function render($pdf, $preview, $user, array $data = []): void {
        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return;
        }

        $imageinfo = json_decode($this->get_data());

        // If there is no file, we have nothing to display.
        if (empty($imageinfo->filename)) {
            return;
        }

        if ($file = $this->get_file()) {
            $location = make_request_directory() . '/target';
            $file->copy_content_to($location);

            // Set the image to the size of the PDF page.
            $mimetype = $file->get_mimetype();
            if ($mimetype == 'image/svg+xml') {
                $pdf->ImageSVG($location, 0, 0, $pdf->getPageWidth(), $pdf->getPageHeight());
            } else {
                $pdf->Image($location, 0, 0, $pdf->getPageWidth(), $pdf->getPageHeight());
            }
        }
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     *
     * @return string the html
     * @throws \dml_exception
     */
    public function render_html(): string {
        global $DB;

        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return '';
        }

        $imageinfo = json_decode($this->get_data());

        // If there is no file, we have nothing to display.
        if (empty($imageinfo->filename)) {
            return '';
        }

        if ($file = $this->get_file()) {
            $url = \moodle_url::make_pluginfile_url($file->get_contextid(), 'enrol_coursepayment', 'image', $file->get_itemid(),
                $file->get_filepath(), $file->get_filename());
            // Get the page we are rendering this on.
            $page = $DB->get_record('coursepayment_pages', ['id' => $this->get_pageid()], '*', MUST_EXIST);

            // Set the image to the size of the page.
            $style = 'width: ' . $page->width . 'mm; height: ' . $page->height . 'mm';

            return \html_writer::tag('img', '', ['src' => $url, 'style' => $style]);
        }

        return '';
    }

}

