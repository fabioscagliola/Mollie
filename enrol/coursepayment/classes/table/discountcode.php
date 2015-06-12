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
 * table discount code
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MoodleFreak.com
 * @author    Luuk Verhoeven
 */

namespace enrol_coursepayment\table;

defined('MOODLE_INTERNAL') || die();

/**
 * Simple subclass of {@link table_sql} that provides
 * some custom formatters for various columns, in order
 * to make the main outstations list nicer
 */
class discountcode extends \table_sql {


    /**
     * list of all courses
     *
     * @var array
     */
    protected $courses = array();


    /**
     * __constructor
     *
     * @param string $uniqueid
     *
     * @global moodle_database $DB
     */
    function __construct($uniqueid) {
        parent::__construct($uniqueid);

        global $DB;
        $qr = $DB->get_recordset('course', null, 'fullname ASC', 'id,fullname');
        foreach ($qr as $row) {
            $this->courses[$row->id] = $row->fullname;
        }
        $qr->close();
    }

    /**
     * Render output for row action
     * @param $row
     *
     * @return string
     * @throws \coding_exception
     */
    protected function col_action($row) {

        $delete = new \moodle_url('/enrol/coursepayment/view/discountcode.php', array(
            'id' => $row->id,
            'action' => 'delete',
        ));

        $edit = new \moodle_url('/enrol/coursepayment/view/discountcode.php', array(
            'id' => $row->id,
            'action' => 'edit',
        ));

        return \html_writer::link($edit, get_string('edit'), array('class' => 'btn btn-small')) . ' &nbsp; ' . \html_writer::link($delete, get_string('delete'), array('class' => 'delete btn btn-small btn-danger'));
    }

    /**
     * Render output for row courseid
     * @param $row
     *
     * @return string
     * @throws \coding_exception
     */
    protected function col_courseid($row) {
        return !empty($this->courses[$row->courseid]) ? $this->courses[$row->courseid] : get_string('form:allcourses', 'enrol_coursepayment');
    }

    /**
     * Render output for row start_time
     * @param $row
     *
     * @return bool|string
     */
    protected function col_start_time($row) {
        return date('d-m-Y', $row->start_time);
    }

    /**
     * @param $row
     *
     * @return bool|string
     */
    protected function col_end_time($row) {
        return date('d-m-Y', $row->end_time);
    }

    /**
     * @param $row
     *
     * @return string
     */
    protected function col_amount($row) {
        return ($row->percentage > 0) ? $row->percentage . ' %' : $row->amount;
    }
}