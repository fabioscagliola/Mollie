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

namespace enrol_coursepayment\invoice\element\invoiceinfo;

use enrol_coursepayment\invoice\element_helper;

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
     *
     * @param array $data
     *
     * @throws \dml_exception
     */
    public function render($pdf, $preview, $user, array $data = []): void {
        global $PAGE;

        $renderer = $PAGE->get_renderer('enrol_coursepayment');
        $text = $renderer->render_template('enrol_coursepayment/element_invoiceinfo', $this->get_invoiceinfo($data));

        element_helper::render_content($pdf, $this, $text);
    }

    /**
     * get_invoiceinfo
     *
     * @param array $data
     *
     * @return object
     * @throws \dml_exception
     */
    public function get_invoiceinfo(array $data = []): object {

        // We are testing.
        if (empty($data)) {
            return $this->dummy_data();
        }

        $pluginconfig = $data['pluginconfig'];
        $invoiceinfo = (object) [
            'companyname' => $pluginconfig->companyname,
            'address' => $pluginconfig->address,
            'place' => $pluginconfig->place,
            'zipcode' => $pluginconfig->zipcode,
            'kvk' => $pluginconfig->kvk,
            'currency' => $pluginconfig->currency ?? 'EUR',
            'date' => date('d-m-Y, H:i'),
            'description' => false,
        ];

        $invoiceinfo->invoice_number = $data['a']->invoice_number;

        if ($invoiceinfo->invoice_number !== $data['a']->description) {
            $invoiceinfo->description = $data['a']->description;
        }

        return $invoiceinfo;
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
        global $PAGE;
        // Dummy.
        $invoiceinfo = $this->dummy_data();

        $renderer = $PAGE->get_renderer('enrol_coursepayment');

        return $renderer->render_template('enrol_coursepayment/element_invoiceinfo', $invoiceinfo);
    }

    /**
     * Dummy data for testing.
     *
     * @return \stdClass
     * @throws \dml_exception
     */
    public function dummy_data(): \stdClass {
        $pluginconfig = get_config('enrol_coursepayment');
        $invoiceinfo = (object) [
            'companyname' => $pluginconfig->companyname ?? 'Company Name',
            'address' => $pluginconfig->address ?? 'Address',
            'place' => $pluginconfig->place ?? 'Place',
            'zipcode' => $pluginconfig->zipcode ?? 'Zipcode',
            'kvk' => $pluginconfig->kvk ?? '23456789',
            'currency' => $pluginconfig->currency ?? 'EUR',
            'date' => date('d-m-Y, H:i'),
        ];
        $invoiceinfo->description = 'TEST';
        $invoiceinfo->invoice_number = 'CPAY' . date("Y") . sprintf('%08d', 1);

        return $invoiceinfo;
    }

}
