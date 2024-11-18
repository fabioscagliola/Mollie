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
 * Discount code class
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */

/**
 * Class enrol_coursepayment_discountcode
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */
class enrol_coursepayment_discountcode {

    /**
     * course id
     *
     * @var int
     */
    protected int $courseid = 0;

    /**
     * The discountcode
     *
     * @var string
     */
    protected string $discountcode = '';

    /**
     * last error container
     *
     * @var string
     */
    protected string $lasterror = '';

    /**
     * discountcode record container
     *
     * @var object|bool
     */
    protected $record = false;

    /**
     * __construct with discountcode and courseid
     *
     * @param string $discount
     * @param int $courseid
     */
    public function __construct($discount = '', $courseid = 0) {

        // Make sure not surrounded by white spaces or tabs if copy pasted.
        $this->discountcode = trim($discount);
        $this->courseid = (int) $courseid;

    }

    /**
     * get the Discountcode set a error message on error
     *
     * @return false|object
     * @throws dml_exception
     */
    public function get_discountcode() {
        global $DB;

        if ($this->record) {
            return $this->record;
        }

        $now = time();
        $row = $DB->get_record('enrol_coursepayment_discount', ['code' => $this->discountcode]);

        if (!$row) {
            $this->lasterror = 'no_record';

            return false;
        }

        if ($row->start_time > $now || $now > $row->end_time) {
            $this->lasterror = 'not_within_the_time_period';

            return false;
        }

        // Wrong course.
        if ($row->courseid != 0 && $this->courseid != $row->courseid) {
            $this->lasterror = 'not_for_this_course';

            return false;
        }

        $this->record = $row;

        return $this->record;
    }

    /**
     * last error message
     *
     * @return string
     * @throws coding_exception
     */
    public function get_last_error(): string {
        return (!empty($this->lasterror)) ? get_string('error:' . $this->lasterror, 'enrol_coursepayment') : '';
    }

}
