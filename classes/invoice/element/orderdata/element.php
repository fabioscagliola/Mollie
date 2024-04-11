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
 * This file contains the coursepayment element orderdata's core interaction API.
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

namespace enrol_coursepayment\invoice\element\orderdata;

/**
 * The  element text's core interaction API.
 *
 * @package    enrol_coursepayment
 * @copyright  2018 MFreak.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \enrol_coursepayment\invoice\element {

    /**
     * This will handle how form data will be saved into the data column in the
     * coursepayment_elements table.
     *
     * @param \stdClass $data the form data
     *
     * @return string the text
     */
    public function save_unique_data($data): string {
        return '';
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
     * @throws \coding_exception
     */
    public function render($pdf, $preview, $user, array $data = []): void {
        global $PAGE;

        if ($preview) {
            $obj = (object) [
                'width' => '480',
                'vat' => 21,
                'subtotal' => number_format(100, 2, ',', ''),
                'total' => number_format(121, 2, ',', ''),
                'vat_price' => number_format(21, 2, ',', ''),
                'coursename' => get_string('orderdata:dummy_course', 'enrol_coursepayment'),
            ];
        } else {
            $obj = (object) [
                'width' => '480',
                'vat' => $data['a']->vatpercentage,
                'subtotal' => $data['a']->costsub,
                'total' => $data['a']->cost,
                'vat_price' => $data['a']->costvat,
                'coursename' => $data['a']->fullcourse,
            ];
        }

        $renderer = $PAGE->get_renderer('enrol_coursepayment');
        $text = $renderer->render_template('enrol_coursepayment/element_orderdata', $obj);

        \enrol_coursepayment\invoice\element_helper::render_content($pdf, $this, $text);
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     *
     * @return string the html
     * @throws \coding_exception
     */
    public function render_html(): string {
        global $PAGE;
        $dummydata = (object) [
            'width' => '500',
            'vat' => 21,
            'subtotal' => number_format(100, 2, ',', ''),
            'total' => number_format(121, 2, ',', ''),
            'vat_price' => number_format(21, 2, ',', ''),
            'coursename' => get_string('orderdata:dummy_course', 'enrol_coursepayment'),
            'color' => $this->get_colour(),
        ];

        $renderer = $PAGE->get_renderer('enrol_coursepayment');

        return $renderer->render_template('enrol_coursepayment/element_orderdata', $dummydata);
    }

}
