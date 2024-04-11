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
 * This file contains the coursepayment element studentname's core interaction API.
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

namespace enrol_coursepayment\invoice\element\studentname;

/**
 * The element studentname's core interaction API.
 *
 * @package    enrol_coursepayment
 * @copyright  2018 MFreak.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \enrol_coursepayment\invoice\element {

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf       the pdf object
     * @param bool $preview   true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     * @param array $data     the data for the element
     *
     * @return void
     */
    public function render($pdf, $preview, $user, array $data = []): void {
        \enrol_coursepayment\invoice\element_helper::render_content($pdf, $this, fullname($user));
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     *
     * @return string the html
     */
    public function render_html(): string {
        global $USER;

        return \enrol_coursepayment\invoice\element_helper::render_html_content($this, fullname($USER));
    }

}
