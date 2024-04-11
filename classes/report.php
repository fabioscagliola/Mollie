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
 * Report
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 **/

namespace enrol_coursepayment;

use enrol_coursepayment\table\report_courses;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Class report
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 **/
class report {

    /**
     * Table overview courses
     *
     * @param object|null $datafilter
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function table_overview_courses($datafilter): void {
        global $PAGE;
        $columns = [
            'firstname',
            'lastname',
            'course',
            'email',
            'phone1',
            'status',
            'addedon',
        ];

        $table = new report_courses(__FUNCTION__);
        $table->define_baseurl($PAGE->url);

        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('class', 'admintable generaltable mftable');
        $table->initialbars(true); // Always initial bars.
        $table->define_columns($columns);

        $table->define_headers(
            array_map(static function($val) {
                return get_string('heading:table_' . $val, 'enrol_coursepayment');
            }, $columns)
        );

        $table->sortable(true, 'addedon', SORT_DESC);
        $table->collapsible(false);

        // Prepare.
        $table->setup();

        // Add the rows and sort if needed.
        $results = self::get_all_courses_data($datafilter);

        // Navigation.
        $table->pagesize(100, count($results));

        $table->data_sort_and_search($results, $datafilter);

        $table->finish_html();

    }

    /**
     * Get all courses data
     *
     * @param object|null $datafilter
     *
     * @return array
     * @throws \dml_exception
     * @throws \coding_exception
     */
    private static function get_all_courses_data($datafilter): array {
        global $DB;

        $sql = 'SELECT cp.* , u.firstname , u.lastname , u.phone1 , u.phone2 , u.email, c.fullname as course
                FROM {enrol_coursepayment} cp
                JOIN {course} c ON (c.id = cp.courseid)
                LEFT JOIN {user} u ON (u.id = cp.userid)
                ';

        $results = $DB->get_records_sql($sql);

        if (get_config('enrol_coursepayment', 'report_include_none_payment_users') == 1
            && empty($datafilter->courseid)) {

            // Build user_id set.
            $userids = [];
            foreach ($results as $result) {
                $userids[$result->userid] = $result->userid;
            }

            if (empty($userids)) {
                return [];
            }

            [$insql, $params] = $DB->get_in_or_equal(array_keys($userids), SQL_PARAMS_QM, 'param', false);
            $sql = 'SELECT u.id, u.firstname , u.lastname , u.phone1, u.phone2 , u.email , "" as course,
                           "-1" as status , "0" as addedon
                    FROM {user} u
                    WHERE u.id > 1
                    AND u.suspended = 0
                    AND u.deleted = 0
                    AND id ' . $insql;

            $users = $DB->get_records_sql($sql, $params);
            foreach ($users as $user) {
                array_push($results, $user);
            }
        }

        return $results;
    }

}
