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
 * Implementation of the privacy plugin provider.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 **/

namespace enrol_coursepayment\privacy;

use context_course;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Class provider.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 **/
class provider implements \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns metadata about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     *
     * @return collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'enrol_coursepayment',
            [
                'userid' => 'privacy:metadata:enrol_coursepayment:userid',
                'orderid' => 'privacy:metadata:enrol_coursepayment:orderid',
                'gateway_transaction_id' => 'privacy:metadata:enrol_coursepayment:gateway_transaction_id',
                'instanceid' => 'privacy:metadata:enrol_coursepayment:instanceid',
                'courseid' => 'privacy:metadata:enrol_coursepayment:courseid',
                'addedon' => 'privacy:metadata:enrol_coursepayment:addedon',
            ],
            'privacy:metadata:enrol_coursepayment'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     *
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        // Enrol has no context ... we work with enrol id instead.
        $sql = "SELECT enrol.id
                  FROM {enrol} enrol
            INNER JOIN {enrol_coursepayment} coursepayment ON (coursepayment.instanceid = enrol.id)
                  WHERE coursepayment.userid = :userid";

        $params = [
            'userid' => $userid,
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the
     * contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;
        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        [$contextsql, $contextparams] = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT coursepayment.*
            FROM {enrol} enrol
            INNER JOIN {enrol_coursepayment} coursepayment ON (coursepayment.instanceid = enrol.id)
            WHERE enrol.id {$contextsql}  AND coursepayment.userid = :userid
            ORDER BY coursepayment.instanceid";

        $params = [
                'contextlevel' => CONTEXT_MODULE,
                'userid' => $user->id,
            ] + $contextparams;

        $coursepayments = $DB->get_recordset_sql($sql, $params);
        foreach ($coursepayments as $coursepayment) {

            // Get context.
            $context = context_course::instance($coursepayment->courseid);

            // Return all references.
            $data = [
                'orderid' => $coursepayment->orderid,
                'gateway_transaction_id' => $coursepayment->gateway_transaction_id,
                'instanceid' => $coursepayment->instanceid,
                'courseid' => $coursepayment->courseid,
                'addedon' => \core_privacy\local\request\transform::datetime($coursepayment->addedon),
            ];

            // Fetch the generic module data.
            $contextdata = helper::get_context_data($context, $user);

            // Merge data and write it.
            $contextdata = (object) array_merge((array) $contextdata, $data);
            writer::with_context($context)->export_data([], $contextdata);
        }

        $coursepayments->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     *
     * @return void
     * @throws \dml_exception
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        // Can't delete you administration.
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     *
     * @return void
     * @throws \dml_exception
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        // Can't delete you administration.
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin
     *                           combination.
     *
     * @return void
     */
    public static function get_users_in_context(userlist $userlist): void {
        // Can't delete you administration.
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     *
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        // Can't delete you administration.
    }

}
