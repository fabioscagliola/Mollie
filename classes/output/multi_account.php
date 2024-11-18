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
 * multi_account output
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2017 MFreak.nl
 * @author    Luuk Verhoeven
 **/

namespace enrol_coursepayment\output;

use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Class multi_account
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2017 MFreak.nl
 * @author    Luuk Verhoeven
 **/
class multi_account implements renderable, templatable {

    /**
     * Export this data, so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     *
     * @return stdClass
     * @throws \dml_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $DB;
        $multiaccounts = $DB->get_records('coursepayment_multiaccount');

        $object = new stdClass();
        $object->data = array_values($multiaccounts);

        return $object;
    }

}
