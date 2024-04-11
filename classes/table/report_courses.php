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
 * report_courses
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 **/

namespace enrol_coursepayment\table;

use enrol_coursepayment_gateway;

/**
 * Class report_courses
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 **/
class report_courses extends \flexible_table {

    /**
     * active_courses constructor.
     *
     * @param int $uniqueid
     */
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);

        require_once(__DIR__ . '/sort.php');
        $this->request = [
            TABLE_VAR_SORT => 'tsort',
            TABLE_VAR_HIDE => 'thide',
            TABLE_VAR_SHOW => 'tshow',
            TABLE_VAR_IFIRST => 'tifirst',
            TABLE_VAR_ILAST => 'tilast',
            TABLE_VAR_PAGE => 'page',
            TABLE_VAR_RESET => 'reset',
            TABLE_VAR_DIR => 'dir',
        ];
    }

    /**
     * small hack to allow flexible_table php sorting with already parsed data
     *
     * @param array $data
     * @param bool|array|object $filterdata
     *
     * @return void
     */
    public function data_sort_and_search(array $data = [], $filterdata = false): void {

        // Allow coll sorting.
        $sort = $this->get_sort_for_table($this->uniqueid);

        if (!empty($sort)) {
            // We have a sort, we need sort now.
            $arr = explode(',', $sort);
            [$column, $direction] = explode(' ', $arr[0]);
            if (isset($this->columns[$column])) {

                $data = arraysortutil::multisort($data, [
                    [
                        "field" => $column,
                        "order" => ($direction == 'ASC' ? false : true),
                    ],
                ]);
            }
        }

        // Search for a value.
        if ($filterdata) {
            $data = $this->search($filterdata, $data);
            $this->totalrows = count($data);
        }

        // Pagination.
        if ($this->totalrows > $this->pagesize) {
            $start = $this->pagesize * $this->currpage;
            $data = array_slice($data, $start, $this->pagesize);
        }

        // Render the table with new ordering.
        foreach ($data as $row) {

            if (empty($row)) {
                continue;
            }

            // Map keys to decorators.
            array_walk($row, function(&$value, $key) use ($row) {

                // Real magic :).
                if (is_callable([$this, 'col_' . $key])) {
                    $value = $this->{'col_' . $key}($row);
                }
            });

            // Add the data to the table.
            $this->add_data_keyed($row);
        }
    }

    /**
     * Search in array
     *
     * @param array|object $filterdata The string array element to search for
     * @param array $stack       The stack to search within for the child
     *
     * @return array
     */
    protected function search($filterdata, array $stack): array {

        $needed = count((array) $filterdata) - 1; // Total search filters - submit_btn.

        return array_filter($stack, static function($row) use ($filterdata, $needed) {
            $matches = 0;
            // Loop throw the filtering.
            foreach ($filterdata as $key => $value) {

                // No action needed.
                if ($value == '') {
                    $matches++;
                    continue;
                }

                // Make sure value match.
                if (isset($row->$key)) {
                    if (stristr($value, $row->$key)) {
                        $matches++;
                    }
                }

                // Search in all.
                if ($key == 'search') {
                    foreach ($row as $v) {
                        if (stristr($v, $value)) {
                            $matches++;
                            break;
                        }
                    }
                }
            }

            // Must match all statements.
            if ($needed == $matches) {
                return true;
            }
        });
    }

    /**
     * Always try to find a phone
     *
     * @param \stdClass $row
     *
     * @return string
     */
    public function col_phone1(\stdClass $row): string {
        return !empty($row->phone1) ? $row->phone1 : $row->phone2;
    }

    /**
     * Payment status
     *
     * @param \stdClass $row
     *
     * @return string
     * @throws \coding_exception
     */
    public function col_status(\stdClass $row): string {

        switch ($row->status) {

            case enrol_coursepayment_gateway::PAYMENT_STATUS_CANCEL:
                return \html_writer::span(get_string('status:cancel', 'enrol_coursepayment'),
                    'badge badge-danger');
                break;

            case enrol_coursepayment_gateway::PAYMENT_STATUS_SUCCESS:
                return \html_writer::span(get_string('status:success', 'enrol_coursepayment'),
                    'badge badge-success');
                break;

            case enrol_coursepayment_gateway::PAYMENT_STATUS_ERROR:
                return \html_writer::span(get_string('status:error', 'enrol_coursepayment'),
                    'badge badge-warning');
                break;

            case enrol_coursepayment_gateway::PAYMENT_STATUS_ABORT:
                return \html_writer::span(get_string('status:abort', 'enrol_coursepayment'),
                    'badge badge-danger');
                break;

            case enrol_coursepayment_gateway::PAYMENT_STATUS_WAITING:
                return \html_writer::span(get_string('status:waiting', 'enrol_coursepayment'),
                    'badge badge-info');
                break;
            case '-1':
                return \html_writer::span(get_string('status:no_payments', 'enrol_coursepayment'),
                    'badge badge-warning');
                break;

        }

        return '';
    }

    /**
     * Added on
     *
     * @param \stdClass $row
     *
     * @return false|string
     */
    public function col_addedon(\stdClass $row) {
        return date('d.m.Y H:i:s', $row->addedon);
    }

}
