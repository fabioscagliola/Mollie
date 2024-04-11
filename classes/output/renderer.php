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
 * Renderer
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2017 MFreak.nl
 * @author    Luuk Verhoeven
 **/

namespace enrol_coursepayment\output;

use plugin_renderer_base;

/**
 * Class renderer
 *
 * @package   enrol_coursepayment
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2017 MFreak.nl
 * @author    Luuk Verhoeven
 */
class renderer extends plugin_renderer_base {

    /**
     * Render a template
     *
     * @param string    $string
     * @param \stdClass $dummydata
     *
     * @return string|boolean
     * @throws \moodle_exception
     */
    public function render_template(string $string, \stdClass $dummydata) {
        return $this->render_from_template($string, $dummydata);
    }

    /**
     * Render multi account
     *
     * @param multi_account $renderable
     *
     * @return string|boolean
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function render_multi_account(multi_account $renderable) {
        $data = $renderable->export_for_template($this);

        return $this->render_from_template('enrol_coursepayment/multi_account_table', $data);
    }
}
