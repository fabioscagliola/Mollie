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
 * this is the abstract class for the gateway
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */

use enrol_coursepayment\invoice\template;

/**
 * Class enrol_coursepayment_gateway
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */
abstract class enrol_coursepayment_gateway {

    /**
     * Payment is aborted
     */
    public const PAYMENT_STATUS_ABORT = 0;

    /**
     * Payment is done successfully
     */
    public const PAYMENT_STATUS_SUCCESS = 1;

    /**
     * Payment was cancelled
     */
    public const PAYMENT_STATUS_CANCEL = 2;

    /**
     * Payment not finished because of a error/exception
     */
    public const PAYMENT_STATUS_ERROR = 3;

    /**
     * Payment is waiting
     */
    public const PAYMENT_STATUS_WAITING = 4;

    /**
     * The prefix that would be prepended to invoice number
     */
    public const INVOICE_PREFIX = 'CPAY';

    /**
     * Name of the gateway
     *
     * @var string
     */
    protected $name = "";

    /**
     * This will contain the gateway their settings
     *
     * @var null|object
     */
    protected $config = null;

    /**
     * Cache the config of the plugin complete
     *
     * @var stdClass|bool
     */
    protected $pluginconfig = false;

    /**
     * Show more debug messages to the user inline only for testing purposes
     *
     * @var bool
     */
    protected $showdebug = false;

    /**
     * Set the gateway on sandbox mode this will be handy for testing purposes !important fake transactions will be
     * enrolled in a course
     *
     * @var bool
     */
    protected $sandbox = false;

    /**
     * Log messages
     *
     * @var string
     */
    protected $log = '';

    /**
     * Multi-account data
     *
     * @var null
     */
    protected $multiaccount = null;

    /**
     * This will contain all values about the course, instance, price
     *
     * @var object
     */
    protected $instanceconfig;

    /**
     * Constructor
     */
    public function __construct() {
        // Load the config always when class is called we will need the settings/credentials.
        $this->get_config();
    }

    /**
     * Validate if a payment provider has a valid ip address
     *
     * @return boolean
     */
    public function ip_validation(): bool {
        // The rationale people give for requesting and using that IP information is for whitelisting purposes.
        // The thought being that by actively denying any requests from other IPs they hope
        // to secure their website from hackers that might be trying to get a paid order without making an actual payment.
        // However, this IP check is not required since the webhook script will always need to actively fetch the payment from the Mollie API,
        // and check its status that way. If you are whitelisting and Mollie ever changes IPs, you might miss this news and be left with a broken store.
        // Without improved security or any other benefit.
        return true;
    }

    /**
     * Add new course order from a user
     *
     * @return array
     */
    abstract public function new_order_course(): array;

    /**
     * Add new activity order from a user
     *
     * @return array
     */
    abstract public function new_order_activity(): array;

    /**
     * Handle the return of payment provider
     *
     * @return bool
     */
    abstract public function callback(): bool;

    /**
     * Render the order_form of the gateway to allow order
     *
     * @param bool $standalone
     *
     * @return string
     */
    abstract public function order_form(bool $standalone = false): string;

    /**
     * Check if a order is valid
     *
     * @param string $orderid
     *
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public function validate_order($orderid = ''): array {
        global $DB;
        $row = $DB->get_record('enrol_coursepayment', [
            'orderid' => $orderid,
            'gateway' => $this->name,
        ]);

        if ($row && empty($row->cost)) {

            $obj = new stdClass();
            $obj->id = $row->id;
            $obj->timeupdated = time();
            $obj->status = self::PAYMENT_STATUS_SUCCESS;
            $DB->update_record('enrol_coursepayment', $obj);

            // This is 0 cost order.
            $this->enrol($row);

            return ['status' => true];
        }

        return [];
    }

    /**
     * Add a payment button for this gateway
     *
     * @return string
     * @throws coding_exception
     */
    public function show_payment_button(): string {

        if (empty($this->config->enabled)) {
            return '';
        }

        return '<div align="center"><form class="coursepaymentbox cpbbottom pt-3 pb-3" action="" method="post">
                    <input type="hidden" name="gateway" value="' . $this->name . '"/>
                    <input type="submit" class="form-submit btn btn-primary coursepayment-btn"  value="' .
            get_string('gateway_' . $this->name . '_send_button', "enrol_coursepayment") . '" />
                </form></div><hr/>';
    }

    /**
     * Load payment provider settings
     *
     * @return void
     */
    protected function get_config(): void {

        $this->pluginconfig = get_config("enrol_coursepayment");

        // Used for removing gateway prefix in the plugin.
        $stripcount = strlen('gateway_' . $this->name . '_');
        $this->config = new stdClass();

        foreach ($this->pluginconfig as $key => $value) {

            // Adding the correct settings to the gateway.
            if (stristr($key, 'gateway_' . $this->name . '_')) {
                $k = substr($key, $stripcount);
                $this->config->{$k} = $value;
            }
        }

        // Check if we need to override with multi-account data.
        $this->load_multi_account_config();
    }

    /**
     * Check if the purchase page is in standalone mode
     *
     * @return bool
     */
    public function is_standalone_purchase_page(): bool {
        return !empty($this->pluginconfig->standalone_purchase_page);
    }

    /**
     * Show debug
     *
     * @param bool $debug
     */
    public function show_debug(bool $debug = false): void {
        $this->showdebug = $debug;
    }

    /**
     * Add message to the log
     *
     * @param mixed $var
     */
    protected function log($var): void {
        $this->log .= date('d-m-Y H:i:s') . ' | Gateway:' . $this->name . ' = ' .
            (is_string($var) ? $var : print_r($var, true)) . PHP_EOL; // @codingStandardsIgnoreLine
    }

    /**
     * Render log if is enabled in the plugin settings
     */
    public function __destruct() {
        if (!empty($this->pluginconfig->debug) && !empty($this->log)) {
            echo '<pre>';
            print_r($this->log); // @codingStandardsIgnoreLine
            echo '</pre>';
        }
    }

    /**
     * Create a new order for a user
     *
     * @param array $data
     *
     * @return array
     * @throws dml_exception
     */
    protected function create_new_course_order_record($data = []): array {
        global $DB;

        $cost = $this->instanceconfig->cost;

        $orderidentifier = uniqid(time()); // @codingStandardsIgnoreLine

        $obj = new stdClass();

        if (!empty($data['discount'])) {
            $discount = $data['discount'];
            $obj->discountdata = serialize($discount);

            // We have discount data.
            if ($discount->percentage > 0) {
                $cost = round($cost / 100 * (100 - $discount->percentage), 2);
            } else {
                $cost = round($cost - $discount->amount);
            }

            // Make sure not below 0.
            if ($cost <= 0) {
                $cost = 0;
            }
        }

        $obj->orderid = $orderidentifier;
        $obj->gateway_transaction_id = '';
        $obj->invoice_number = 0;
        $obj->gateway = $this->name;
        $obj->addedon = time();
        $obj->timeupdated = 0;
        $obj->userid = $this->instanceconfig->userid;

        if (!empty($this->pluginconfig->multi_account)) {
            $obj->profile_data = enrol_coursepayment_helper::get_profile_field_data($this->pluginconfig->multi_account_fieldid,
                $this->instanceconfig->userid);
        }

        $obj->courseid = $this->instanceconfig->courseid;
        $obj->instanceid = $this->instanceconfig->instanceid;
        $obj->cost = $cost;
        $obj->vatpercentage = is_numeric($this->instanceconfig->customint1) ? $this->instanceconfig->customint1 :
            $this->pluginconfig->vatpercentage;
        $obj->status = self::PAYMENT_STATUS_WAITING;
        $id = $DB->insert_record('enrol_coursepayment', $obj);

        return [
            'orderid' => $orderidentifier,
            'id' => $id,
            'cost' => $cost,
        ];
    }

    /**
     * Create a new order for a user
     *
     * @param array $data
     *
     * @return array
     * @throws dml_exception
     */
    protected function create_new_activity_order_record($data = []): array {
        global $DB;

        $cost = $this->instanceconfig->cost;
        $obj = new stdClass();

        if (!empty($data['discount'])) {
            $discount = $data['discount'];
            $obj->discountdata = serialize($discount);
            // We have discount data.
            if ($discount->percentage > 0) {
                $cost = round($cost / 100 * (100 - $discount->percentage), 2);
            } else {
                $cost = round($cost - $discount->amount);
            }
            // Make sure not below 0.
            if ($cost <= 0) {
                $cost = 0;
            }
        }

        $orderidentifier = uniqid(time()); // @codingStandardsIgnoreLine
        $obj->orderid = $orderidentifier;
        $obj->gateway_transaction_id = '';
        $obj->invoice_number = 0;
        $obj->gateway = $this->name;
        $obj->addedon = time();
        $obj->timeupdated = 0;
        $obj->userid = $this->instanceconfig->userid;
        $obj->courseid = $this->instanceconfig->courseid;
        $obj->cmid = $this->instanceconfig->cmid;
        $obj->instanceid = 0;
        $obj->is_activity = 1;
        $obj->cost = $cost;

        if (!empty($this->pluginconfig->multi_account)) {
            $obj->profile_data = enrol_coursepayment_helper::get_profile_field_data($this->pluginconfig->multi_account_fieldid,
                $this->instanceconfig->userid);
        }

        $obj->vatpercentage = is_numeric($this->instanceconfig->customint1) ? $this->instanceconfig->customint1 :
            $this->pluginconfig->vatpercentage;

        $obj->status = self::PAYMENT_STATUS_WAITING;
        $obj->section = isset($this->instanceconfig->section) ? $this->instanceconfig->section : -10;
        $id = $DB->insert_record('enrol_coursepayment', $obj);

        return [
            'orderid' => $orderidentifier,
            'id' => $id,
            'cost' => $cost,
        ];
    }

    /**
     * Set instance config
     *
     * @param object $config
     */
    public function set_instanceconfig($config): void {
        $this->instanceconfig = (object) $config;
    }

    /**
     * Enrol a user to the course use enrol_coursepayment record
     *
     * @param object $record
     *
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function enrol($record = null): bool {
        global $DB, $CFG;

        if (empty($record)) {
            return false;
        }

        // Doesn't need a enrolment.
        if (!empty($record->is_activity)) {
            return true;
        }

        if (file_exists($CFG->libdir . '/eventslib.php')) {
            require_once($CFG->libdir . '/eventslib.php');
        }
        require_once($CFG->libdir . '/enrollib.php');
        require_once($CFG->libdir . '/filelib.php');

        $plugin = enrol_get_plugin('coursepayment');

        // First we need all the data to enrol.
        $plugininstance = $DB->get_record("enrol", ["id" => $record->instanceid, "status" => 0]);
        $user = $DB->get_record("user", ['id' => $record->userid, 'deleted' => 0]);

        if (empty($user)) {
            // Skip deleted users.
            return true;
        }

        $course = $DB->get_record('course', ['id' => $record->courseid]);
        $context = context_course::instance($course->id, IGNORE_MISSING);

        if ($plugininstance->enrolperiod) {
            $timestart = time();
            $timeend = $timestart + $plugininstance->enrolperiod;
        } else {
            $timestart = 0;
            $timeend = 0;
        }

        // Enrol user.
        $plugin->enrol_user($plugininstance, $user->id, $plugininstance->roleid, $timestart, $timeend);

        // Send messages about the enrolment.
        $this->enrol_mail($plugin, $course, $context, $user);

        return true;
    }

    /**
     * Enrol mail
     *
     * @param enrol_plugin $plugin
     * @param object $course
     * @param context $context
     * @param object $user
     *
     * @return void
     * @throws coding_exception
     */
    protected function enrol_mail($plugin, $course, $context, $user): void {
        global $CFG;
        $teacher = false;

        // Pass $view=true to filter hidden caps if the user cannot see them.
        if ($users = get_users_by_capability($context, 'moodle/course:update',
            'u.*', 'u.id ASC', '', '', '', '', false, true)) {
            $users = sort_by_roleassignment_authority($users, $context);
            $teacher = array_shift($users);
        }

        $mailstudents = $plugin->get_config('mailstudents');
        $mailteachers = $plugin->get_config('mailteachers');
        $mailadmins = $plugin->get_config('mailadmins');

        $shortname = format_string($course->shortname, true, ['context' => $context]);
        $a = new stdClass();

        if (!empty($mailstudents)) {
            $a->coursename = format_string($course->fullname, true, ['context' => $context]);
            $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id";

            $eventdata = enrol_coursepayment_helper::get_event_object();
            $eventdata->modulename = 'moodle';
            $eventdata->component = 'enrol_coursepayment';
            $eventdata->name = 'coursepayment_enrolment';
            $eventdata->userfrom = empty($teacher) ? get_admin() : $teacher;
            $eventdata->userto = $user;
            $eventdata->subject = get_string("enrolmentnew", 'enrol', $shortname);
            $eventdata->fullmessage = get_string('welcometocoursetext', '', $a);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml = '';
            $eventdata->smallmessage = '';
            message_send($eventdata);
        }

        if (!empty($mailteachers) && !empty($teacher)) {
            $a->course = format_string($course->fullname, true, ['context' => $context]);
            $a->user = fullname($user);

            $eventdata = enrol_coursepayment_helper::get_event_object();
            $eventdata->modulename = 'moodle';
            $eventdata->component = 'enrol_coursepayment';
            $eventdata->name = 'coursepayment_enrolment';
            $eventdata->userfrom = $user;
            $eventdata->userto = $teacher;
            $eventdata->subject = get_string("enrolmentnew", 'enrol', $shortname);
            $eventdata->fullmessage = get_string('enrolmentnewuser', 'enrol', $a);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml = '';
            $eventdata->smallmessage = '';
            message_send($eventdata);
        }

        if (!empty($mailadmins)) {
            $a->course = format_string($course->fullname, true, ['context' => $context]);
            $a->user = fullname($user);
            $admins = get_admins();
            foreach ($admins as $admin) {
                $eventdata = enrol_coursepayment_helper::get_event_object();
                $eventdata->modulename = 'moodle';
                $eventdata->component = 'enrol_coursepayment';
                $eventdata->name = 'coursepayment_enrolment';
                $eventdata->userfrom = $user;
                $eventdata->userto = $admin;
                $eventdata->subject = get_string("enrolmentnew", 'enrol', $shortname);
                $eventdata->fullmessage = get_string('enrolmentnewuser', 'enrol', $a);
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml = '';
                $eventdata->smallmessage = '';
                message_send($eventdata);
            }
        }
    }

    /**
     * Send invoice to the customer, teacher and extra mail-accounts
     *
     * @param stdClass $coursepayment
     * @param string $method
     *
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    protected function send_invoice(stdClass $coursepayment, $method = ''): bool {
        global $DB, $CFG;

        if (empty($coursepayment)) {
            return false;
        }

        if (file_exists($CFG->libdir . '/eventslib.php')) {
            // Not available in moodle 3.6.
            require_once($CFG->libdir . '/eventslib.php');
        }

        require_once($CFG->libdir . '/enrollib.php');
        require_once($CFG->libdir . '/filelib.php');

        $user = $DB->get_record("user", ['id' => $coursepayment->userid]);
        $course = $DB->get_record('course', ['id' => $coursepayment->courseid]);
        $context = context_course::instance($course->id, IGNORE_MISSING);

        $a = $this->get_invoice_strings($user, $course, $coursepayment, $method);

        // Generate PDF invoice.
        $file = template::render($coursepayment, $user, $this->pluginconfig, $a);

        if (!empty($this->pluginconfig->mailstudents_invoice)) {

            $eventdata = enrol_coursepayment_helper::get_event_object();
            $eventdata->modulename = 'moodle';
            $eventdata->component = 'enrol_coursepayment';
            $eventdata->name = 'coursepayment_invoice';
            $eventdata->userfrom = core_user::get_support_user();
            $eventdata->userto = $user;
            $eventdata->subject = get_string("mail:invoice_subject_student", 'enrol_coursepayment', $a);
            $eventdata->fullmessage = html_to_text(get_string('mail:invoice_message_student', 'enrol_coursepayment', $a));
            $eventdata->fullmessageformat = FORMAT_HTML;
            $eventdata->fullmessagehtml = get_string('mail:invoice_message_student', 'enrol_coursepayment', $a);
            $eventdata->smallmessage = '';
            $eventdata->attachment = $file;
            $eventdata->attachname = $a->invoice_number . '.pdf';

            message_send($eventdata);
        }

        if (!empty($this->pluginconfig->mailteachers_invoice)) {

            // Getting the teachers.
            if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*', 'u.id ASC',
                '', '', '', '', false, true)) {
                $users = sort_by_roleassignment_authority($users, $context);
                $teacher = array_shift($users);
            } else {
                $teacher = false;
            }

            if (!empty($teacher)) {

                $eventdata = enrol_coursepayment_helper::get_event_object();
                $eventdata->modulename = 'moodle';
                $eventdata->component = 'enrol_coursepayment';
                $eventdata->name = 'coursepayment_invoice';
                $eventdata->userfrom = core_user::get_support_user();
                $eventdata->userto = $teacher;
                $eventdata->subject = get_string("mail:invoice_subject_teacher", 'enrol_coursepayment', $a);
                $eventdata->fullmessage = html_to_text(get_string('mail:invoice_message_teacher', 'enrol_coursepayment', $a));
                $eventdata->fullmessageformat = FORMAT_HTML;
                $eventdata->fullmessagehtml = get_string('mail:invoice_message_teacher', 'enrol_coursepayment', $a);
                $eventdata->smallmessage = '';
                $eventdata->attachment = $file;
                $eventdata->attachname = $a->invoice_number . '.pdf';
                message_send($eventdata);
            }
        }

        if (!empty($this->pluginconfig->mailadmins_invoice)) {

            $admins = get_admins();
            foreach ($admins as $admin) {
                $eventdata = enrol_coursepayment_helper::get_event_object();
                $eventdata->modulename = 'moodle';
                $eventdata->component = 'enrol_coursepayment';
                $eventdata->name = 'coursepayment_invoice';
                $eventdata->userfrom = core_user::get_support_user();
                $eventdata->userto = $admin;
                $eventdata->subject = get_string("mail:invoice_subject_admin", 'enrol_coursepayment', $a);
                $eventdata->fullmessage = html_to_text(get_string('mail:invoice_message_admin', 'enrol_coursepayment', $a));
                $eventdata->fullmessageformat = FORMAT_HTML;
                $eventdata->fullmessagehtml = get_string('mail:invoice_message_admin', 'enrol_coursepayment', $a);
                $eventdata->smallmessage = '';
                $eventdata->attachment = $file;
                $eventdata->attachname = $a->invoice_number . '.pdf';
                message_send($eventdata);
            }
        }

        if (!empty($this->pluginconfig->custom_mails_invoice)) {
            $parts = explode(',', $this->pluginconfig->custom_mails_invoice);
            foreach ($parts as $part) {
                $part = trim($part);
                if (filter_var($part, FILTER_VALIDATE_EMAIL)) {
                    // Get temp user object.
                    $dummyuser = new stdClass();
                    $dummyuser->id = 1;
                    $dummyuser->email = $part;
                    $dummyuser->firstname = ' ';
                    $dummyuser->username = ' ';
                    $dummyuser->lastname = '';
                    $dummyuser->confirmed = 1;
                    $dummyuser->suspended = 0;
                    $dummyuser->deleted = 0;
                    $dummyuser->picture = 0;
                    $dummyuser->auth = 'manual';
                    $dummyuser->firstnamephonetic = '';
                    $dummyuser->lastnamephonetic = '';
                    $dummyuser->middlename = '';
                    $dummyuser->alternatename = '';
                    $dummyuser->imagealt = '';
                    $dummyuser->emailstop = 0;

                    $eventdata = enrol_coursepayment_helper::get_event_object();
                    $eventdata->modulename = 'moodle';
                    $eventdata->component = 'enrol_coursepayment';
                    $eventdata->name = 'coursepayment_invoice';
                    $eventdata->userfrom = core_user::get_support_user();
                    $eventdata->userto = $dummyuser;
                    $eventdata->subject = get_string("mail:invoice_subject_manual", 'enrol_coursepayment', $a);
                    $eventdata->fullmessage = html_to_text(get_string('mail:invoice_message_manual',
                        'enrol_coursepayment', $a));
                    $eventdata->fullmessageformat = FORMAT_HTML;
                    $eventdata->fullmessagehtml = get_string('mail:invoice_message_manual', 'enrol_coursepayment', $a);
                    $eventdata->smallmessage = '';
                    $eventdata->attachment = $file;
                    $eventdata->attachname = $a->invoice_number . '.pdf';
                    message_send($eventdata);
                }
            }
        }

        return true;
    }

    /**
     * Add form for when discount code are created
     *
     * @param string $discountcode
     * @param array $status
     *
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function form_discount_code($discountcode = '', $status = []): string {
        global $DB;
        $string = '';

        // Check if there is a discount code.
        $row = $DB->get_record('enrol_coursepayment_discount', [], 'id', IGNORE_MULTIPLE);
        if ($row) {
            $string .= '<hr/>';
            $string .= '<div align="center"><p>' . get_string('discount_code_desc', 'enrol_coursepayment') . '<br/>
                            ' . ((!empty($status['error_discount']) ?
                    '<b style="color:red"  id="error_coursepayment">' . $status['message'] . '</b>' :
                    '<b style="color:red" id="error_coursepayment"></b>')) . '<br/></p>
                            <input type="text" autocomplete="off" name="discountcode" id="discountcode"
                                value="' . $discountcode . '" /><div id="price_holder"></div>
                        </div>';
        }

        return $string;
    }

    /**
     * Get a new invoice_number
     *
     * @return int
     * @throws dml_exception
     */
    protected function get_new_invoice_number(): int {
        global $DB;
        $rows = $DB->get_records('enrol_coursepayment', [], 'invoice_number desc', 'invoice_number', 0, 1);
        if ($rows) {
            $row = reset($rows);

            return $row->invoice_number + 1;
        }

        return 1;
    }

    /**
     * Get a nice format invoice number
     *
     * @param object $record
     *
     * @return string
     */
    protected function get_invoice_number_format($record = null): string {

        if (!empty($record->invoice_number) && !empty($record->addedon)) {
            return self::INVOICE_PREFIX . date("Y", $record->addedon) . sprintf('%08d',
                    $record->invoice_number);
        }

        return 'TEST';
    }

    /**
     * Get payment description
     *
     * @param object $record
     *
     * @return mixed
     * @throws dml_exception
     */
    protected function get_payment_description($record) {
        global $DB, $SITE;

        $obj = new stdClass();
        $obj->invoice_number = $this->get_invoice_number_format($record);

        // Course.
        $obj->course = $DB->get_field('course', 'fullname', ['id' => $record->courseid]);
        $obj->course_shortname = $DB->get_field('course', 'shortname', ['id' => $record->courseid]);

        // Site.
        $obj->site = $SITE->fullname;
        $obj->site_shortname = $SITE->shortname;

        // Add enrolment instance.
        $enrol = $DB->get_record('enrol', ['id' => $record->instanceid], '*');
        if ($enrol) {
            $obj->customtext1 = $enrol->customtext1;
            $obj->customtext2 = $enrol->customtext2;
        } else {
            $obj->customtext1 = '';
            $obj->customtext2 = '';
        }

        // Fallback prevent Mollie issue.
        if (empty($this->pluginconfig->transaction_name)) {
            $this->pluginconfig->transaction_name = '{invoice_number}';
        }

        return enrol_coursepayment_helper::parse_text($this->pluginconfig->transaction_name, $obj);
    }

    /**
     * get correct number format used for pricing
     *
     * @param float|int $number
     *
     * @return string
     */
    public function price($number = 0.00): string {
        return number_format(round($number, 2), 2, ',', ' ');
    }

    /**
     * Add agreement check if needed
     *
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function add_agreement_checkbox(): string {
        $string = '';

        $agreement = get_config('enrol_coursepayment', 'link_agreement');
        if (!empty($agreement)) {
            $obj = new stdClass();
            $obj->link = $agreement;
            $string .= '<hr/>  <div id="coursepayment_agreement_checkbox">
                <input type="checkbox" name="agreement" id="coursepayment_agreement" required>
                <label for="coursepayment_agreement">' .
                get_string('agreement_label', 'enrol_coursepayment', $obj) .
                '</label></div>';
        }

        return $string;
    }

    /**
     * Load multi-account config if needed
     *
     * @param int $userid          only needed when running from cron
     * @param string $profilevalue only needed when running from cron
     *
     * @throws dml_exception
     */
    protected function load_multi_account_config($userid = 0, $profilevalue = ''): void {
        global $USER, $DB;

        // Normally we can $USER only in cron we need to fix this.
        if ($userid == 0) {
            $userid = $USER->id;
        }

        if (!empty($this->pluginconfig->multi_account)) {
            // Check if we match profile value of any of the multi-accounts.
            if (empty($profilevalue)) {
                $profilevalue = enrol_coursepayment_helper::get_profile_field_data($this->pluginconfig->multi_account_fieldid,
                    $userid);
            }

            // Load default multi-account.
            $this->multiaccount = $DB->get_record('coursepayment_multiaccount', ['is_default' => 1],
                '*', MUST_EXIST);

            if (!empty($profilevalue)) {
                // Check if we have a multi-account matching your value.
                $mutiaccount = $DB->get_record('coursepayment_multiaccount', [
                    'profile_value' => $profilevalue,
                ], '*');

                // Found we should use this.
                if (!empty($mutiaccount)) {
                    $this->multiaccount = $mutiaccount;
                }
            }

            // Reset some values that can't be used by multi-account.
            $this->config->enabled = true;
            $this->config->external_connector = 0;

            // Update invoice details.
            $this->pluginconfig->companyname = $this->multiaccount->company_name;
            $this->pluginconfig->address = $this->multiaccount->address;
            $this->pluginconfig->place = $this->multiaccount->place;
            $this->pluginconfig->zipcode = $this->multiaccount->zipcode;
            $this->pluginconfig->kvk = $this->multiaccount->kvk;
            $this->pluginconfig->btw = $this->multiaccount->btw;

            $stripcount = strlen('gateway_' . $this->name . '_');

            // Override the normal settings.
            foreach ($this->multiaccount as $key => $value) {

                // Adding the correct settings to the gateway.
                if (stristr($key, 'gateway_' . $this->name . '_')) {
                    $k = substr($key, $stripcount);
                    $this->config->{$k} = $value;
                }
            }
        }
    }

    /**
     * Make strings for invoice messages and invoice.
     *
     * @param object $user
     * @param object $course
     * @param object $coursepayment
     * @param string $method
     *
     * @return stdClass
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    private function get_invoice_strings($user, $course, $coursepayment, $method): stdClass {
        global $SITE;
        $context = context_course::instance($course->id, IGNORE_MISSING);
        $invoicenumber = $coursepayment->invoice_number;

        // Mail object.
        $a = new stdClass();
        $a->course = format_string($course->fullname, true, ['context' => $context]);
        $a->fullname = fullname($user);
        $a->email = $user->email;
        $a->date = date('d-m-Y, H:i', $coursepayment->addedon);

        // Fix this could also be a activity or section.
        if ($coursepayment->cmid > 0 && $coursepayment->is_activity == 1) {
            $module = enrol_coursepayment_helper::get_cmid_info($coursepayment->cmid, $course->id);
            $a->fullcourse = $module->name . ' - ' . $a->course;
            $a->content_type = get_string('activity');
        } else if ($coursepayment->section > 0) {
            $module = enrol_coursepayment_helper::get_section_info($coursepayment->section, $course->id);
            $a->fullcourse = $module->name . ' - ' . $a->course;
            $a->content_type = get_string('section');
        } else {
            $a->fullcourse = $course->fullname;
            $a->content_type = get_string('course');
        }

        // Set record invoice number this is not done.
        if ($coursepayment->invoice_number == 0) {
            $coursepayment->invoice_number = $invoicenumber;
        }

        $a->invoice_number = $this->get_invoice_number_format($coursepayment);

        // Company data.
        $a->companyname = $this->pluginconfig->companyname;
        $a->address = $this->pluginconfig->address;
        $a->place = $this->pluginconfig->place;
        $a->zipcode = $this->pluginconfig->zipcode;
        $a->kvk = $this->pluginconfig->kvk;
        $a->btw = $this->pluginconfig->btw;
        $a->currency = $this->pluginconfig->currency;
        $a->method = $method;
        $a->description = $this->get_payment_description($coursepayment);

        // Calculate cost.
        $a->vatpercentage = $coursepayment->vatpercentage;

        $vatprice = ($coursepayment->cost / (100 + $a->vatpercentage)) * $a->vatpercentage;

        $a->costvat = $this->price($vatprice);
        $a->cost = $this->price($coursepayment->cost);
        $a->costsub = $this->price($coursepayment->cost - $vatprice);
        $a->sitename = $SITE->fullname;

        return $a;
    }

    /**
     * Get gateway locale
     *
     * @return string
     */
    public function get_gateway_locale(): string {
        return (in_array($this->instanceconfig->locale, [
            'de_DE',
            'en_US',
            'fr_FR',
            'es_ES',
            'nl_NL',
        ]) ? $this->instanceconfig->locale : 'nl_NL');
    }

}
