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
 * Course Payment enrolment
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */

/**
 * Class enrol_coursepayment_plugin
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */
class enrol_coursepayment_plugin extends enrol_plugin {

    /**
     * Returns optional enrolment information icons.
     *
     * This is used in course list for quick overview of enrolment options.
     *
     * We are not using single instance parameter because sometimes
     * we might want to prevent icon repetition when multiple instances
     * of one type exist. One instance may also produce several icons.
     *
     * @param array $instances all enrol instances of this type in one course
     *
     * @return array of pix_icon
     * @throws coding_exception
     */
    public function get_info_icons(array $instances): array {
        return [
            new pix_icon('icon', get_string('pluginname', 'enrol_coursepayment'),
                'enrol_coursepayment'),
        ];
    }

    /**
     * Users with role assign cap may tweak the roles later
     *
     * @return bool means anybody may tweak roles, it does not use itemid and component when assigning roles
     */
    public function roles_protected(): bool {
        return false;
    }

    /**
     * Does this plugin allow manual changes in user_enrolments table?
     *
     * All plugins allowing this must implement 'enrol/xxx:manage' capability
     *
     * @param stdClass $instance course enrol instance
     *
     * @return bool means it is possible to change enrol period and status in user_enrolments table
     */
    public function allow_unenrol(stdClass $instance): bool {
        return true;
    }

    /**
     * Does this plugin allow manual changes in user_enrolments table?
     *
     * All plugins allowing this must implement 'enrol/xxx:manage' capability
     *
     * @param stdClass $instance course enrol instance
     *
     * @return bool means it is possible to change enrol period and status in user_enrolments table
     */
    public function allow_manage(stdClass $instance): bool {
        return true;
    }

    /**
     * Show enrolme link
     *
     * @param stdClass $instance
     *
     * @return bool
     */
    public function show_enrolme_link(stdClass $instance): bool {
        return ($instance->status == ENROL_INSTANCE_ENABLED);
    }

    /**
     * Sets up navigation entries.
     *
     * @param navigation_node $instancesnode
     * @param stdClass $instance
     *
     * @return void
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function add_course_navigation($instancesnode, stdClass $instance): void {
        global $PAGE, $COURSE;
        if ($instance->enrol !== 'coursepayment') {
            throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/coursepayment:report', $context)) {
            $managelink = new moodle_url('/enrol/coursepayment/edit.php', [
                'courseid' => $instance->courseid,
                'id' => $instance->id,
            ]);

            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }

        if (has_capability('enrol/coursepayment:config', $context)) {
            $url = new moodle_url('/enrol/coursepayment/view/report.php', [
                'id' => $COURSE->id,
            ]);

            $pix = new pix_icon('icon', get_string(
                'pluginname',
                'enrol_coursepayment'),
                'enrol_coursepayment');

            // Add to course navigation.
            $PAGE->navigation->find($COURSE->id, navigation_node::TYPE_COURSE)->add(
                get_string('btn:report', 'enrol_coursepayment'),
                $url,
                navigation_node::TYPE_SETTING,
                '',
                '',
                $pix
            );
        }

    }

    /**
     * Returns edit icons for the page with list of instances
     *
     * @param stdClass $instance
     *
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_action_icons(stdClass $instance): array {
        global $OUTPUT;

        if ($instance->enrol !== 'coursepayment') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = [];

        if (has_capability('enrol/coursepayment:config', $context)) {
            $editlink = new moodle_url("/enrol/coursepayment/edit.php", [
                'courseid' => $instance->courseid,
                'id' => $instance->id,
            ]);
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit'),
                'core', ['class' => 'iconsmall']));
        }

        return $icons;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     *
     * @param int $courseid
     *
     * @return moodle_url|null page url
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_newinstance_link($courseid) {
        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context)
            || !has_capability('enrol/coursepayment:config', $context)) {
            return null;
        }

        // Multiple instances supported - different cost for different roles.
        return new moodle_url('/enrol/coursepayment/edit.php', ['courseid' => $courseid]);
    }

    /**
     * Creates course enrol form, checks if form submitted
     * and enrols user if necessary. It can also redirect.
     *
     * @param stdClass $instance
     *
     * @return string html text, usually a form in a text box
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function enrol_page_hook(stdClass $instance) {
        global $USER, $OUTPUT, $DB, $COURSE, $PAGE, $CFG;

        $gatewaymethod = optional_param('gateway', false, PARAM_ALPHA);

        ob_start();

        if ($DB->record_exists('user_enrolments', ['userid' => $USER->id, 'enrolid' => $instance->id])) {
            return ob_get_clean();
        }

        if ($instance->enrolstartdate != 0 && $instance->enrolstartdate > time()) {
            return ob_get_clean();
        }

        if ($instance->enrolenddate != 0 && $instance->enrolenddate < time()) {
            return ob_get_clean();
        }

        $cost = (float) ($instance->cost <= 0) ? $this->get_config('cost') : $instance->cost;

        if (abs($cost) < 0.01 || isguestuser()) {
            // No cost, other enrolment methods (instances) should be used.
            return ob_get_clean();
        }

        // Get the course.
        if ($COURSE->id == $instance->courseid) {
            // Prevent extra query if possible.
            $course = $COURSE;
        } else {
            $course = $DB->get_record('course', ['id' => $instance->courseid], '*', MUST_EXIST);
        }

        // Set main gateway javascript.
        $jsmodule = [
            'name' => 'enrol_coursepayment_gateway',
            'fullpath' => '/enrol/coursepayment/js/gateway.js',
            'requires' => ['node', 'io'],
        ];

        $PAGE->requires->js_init_call('M.enrol_coursepayment_gateway.init', [
            $CFG->wwwroot . '/enrol/coursepayment/ajax.php',
            sesskey(),
            $course->id,
        ], false, $jsmodule);

        $instance->name = ($instance->name) ?? "";

        // Config to send to the gateways.
        $config = new stdClass();
        $config->instanceid = $instance->id;
        $config->courseid = $instance->courseid;
        $config->userid = $USER->id;
        $config->userfullname = fullname($USER);
        $config->currency = $instance->currency;
        $config->cost = $cost;
        $config->instancename = $instance->name;
        $config->localisedcost = format_float($cost, 2, true);
        $config->coursename = $course->fullname;
        $config->locale = $USER->lang;
        $config->customint1 = $instance->customint1;

        // Currency to symbol.
        $locale = 'nl_NL';
        $formatcurrency = new NumberFormatter($locale . "@currency=$instance->currency", NumberFormatter::CURRENCY);
        $currsymbol = $formatcurrency->getSymbol(NumberFormatter::CURRENCY_SYMBOL);

        // You can set a custom text to be shown instead of instance name.
        $name = !empty($instance->customtext1) ? $instance->customtext1 : $config->instancename;
        echo '<div align="center"><h3 class="coursepayment_instancename coursepaymentbox cpbtop m-0 pt-3">' . $name . '</h3>' .
            '<p class="coursepaymentbox cpbmiddle m-0"><b>' . get_string("cost") . ': ' . $currsymbol .
            ' <span id="coursepayment_cost">' . $config->localisedcost . '</span>' . ' </b></p></div>';

        // Payment method is selected.
        if (!empty($gatewaymethod)) {

            $gateway = 'enrol_coursepayment_' . $gatewaymethod;
            if (!class_exists($gateway)) {
                return ob_get_clean();
            }

            // Redirect to a standalone payment page.
            if (!empty($this->get_config('standalone_purchase_page'))) {
                redirect(new moodle_url('/enrol/coursepayment/view/purchase.php', [
                    'instanceid' => $instance->id,
                    'gateway' => $gatewaymethod,
                    'id' => $course->id,
                ]));
            }

            $gateway = new $gateway();
            $gateway->set_instanceconfig($config);
            echo $gateway->order_form();

        } else {

            $allgateways = $this->get_gateways();
            foreach ($allgateways as $gateway => $gatewaystring) {

                // Loop throw all available gateways and add there button to course page.
                $gateway = 'enrol_coursepayment_' . $gateway;
                if (!class_exists($gateway)) {
                    continue;
                }

                $gateway = new $gateway();
                echo $gateway->show_payment_button();
            }
        }

        return $OUTPUT->box(ob_get_clean());
    }

    /**
     * Restore instance and map settings.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $course
     * @param int $oldid
     *
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws restore_step_exception
     */
    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid): void {
        global $DB;
        if ($step->get_task()->get_target() == backup::TARGET_NEW_COURSE) {
            $merge = false;
        } else {
            $merge = [
                'courseid' => $data->courseid,
                'enrol' => $this->get_name(),
                'roleid' => $data->roleid,
                'cost' => $data->cost,
                'currency' => $data->currency,
            ];
        }
        if ($merge && $instances = $DB->get_records('enrol', $merge, 'id')) {
            $instance = reset($instances);
            $instanceid = $instance->id;
        } else {
            $instanceid = $this->add_instance($course, (array) $data);
        }
        $step->set_mapping('enrol', $oldid, $instanceid);
    }

    /**
     * Restore user enrolment.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $instance
     * @param int $userid
     * @param int $oldinstancestatus
     *
     * @return void
     * @throws coding_exception
     */
    public function restore_user_enrolment(restore_enrolments_structure_step $step, $data, $instance, $userid,
        $oldinstancestatus): void {
        $this->enrol_user($instance, $userid, null, $data->timestart, $data->timeend, $data->status);
    }

    /**
     * Gets an array of the user enrolment actions
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     *
     * @return array An array of user_enrolment_actions
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue): array {
        $actions = [];
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol($instance) && has_capability("enrol/coursepayment:unenrol", $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url, [
                'class' => 'unenrollink',
                'rel' => $ue->id,
            ]);
        }
        if ($this->allow_manage($instance) && has_capability("enrol/coursepayment:manage", $context)) {
            $url = new moodle_url('/enrol/editenrolment.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/edit', ''), get_string('edit'), $url, [
                'class' => 'editenrollink',
                'rel' => $ue->id,
            ]);
        }

        return $actions;
    }

    /**
     * Execute synchronisation.
     *
     * @param progress_trace $trace
     *
     * @return int exit code, 0 means ok
     */
    public function sync(progress_trace $trace): int {
        $this->process_expirations($trace);

        return 0;
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param stdClass $instance
     *
     * @return bool
     * @throws coding_exception
     */
    public function can_delete_instance($instance): bool {
        $context = context_course::instance($instance->courseid);

        return has_capability('enrol/coursepayment:config', $context);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     *
     * @return bool
     * @throws coding_exception
     */
    public function can_hide_show_instance($instance): bool {
        $context = context_course::instance($instance->courseid);

        return has_capability('enrol/coursepayment:config', $context);
    }

    /**
     * get all currencies that are supported by this block
     *
     * @return array
     * @throws coding_exception
     */
    public function get_currencies(): array {
        $codes = ['EUR'];
        $currencies = [];
        foreach ($codes as $c) {
            $currencies[$c] = new lang_string($c, 'core_currencies');
        }

        return $currencies;
    }

    /**
     * get all gateways that are available
     *
     * @return array
     */
    public function get_gateways(): array {
        return ['mollie' => 'Mollie'];
    }

    /**
     * validate a order check if this is really paid
     *
     * @param string $orderid
     * @param string $gateway
     *
     * @return array
     * @throws coding_exception
     */
    public function order_valid($orderid = '', $gateway = ''): array {
        $return = [
            'status' => false,
            'message' => '',
        ];

        $gateway = 'enrol_coursepayment_' . $gateway;
        if (!class_exists($gateway)) {
            $return['message'] = get_string('gateway_not_exists', 'enrol_coursepayment');

            return $return;
        }

        $gateway = new $gateway();
        $status = $gateway->validate_order($orderid);

        return [
            'status' => !empty($status['status']),
            'message' => $status['message'] ?? '',
        ];
    }

    /**
     * process orders with the cron if we missed an ipn call.
     * We can query the gateway API to check if something has a new status
     *
     * @throws dml_exception
     */
    public function cron_process_orders(): void {
        global $DB;

        mtrace(__CLASS__ . ' | ' . __FUNCTION__);
        $results = $DB->get_records('enrol_coursepayment', [
            'status' => enrol_coursepayment_gateway::PAYMENT_STATUS_WAITING,
        ], 'id, orderid, gateway');

        if ($results) {
            foreach ($results as $row) {
                $gateway = 'enrol_coursepayment_' . $row->gateway;
                if (!class_exists($gateway)) {
                    continue;
                }

                $gateway = new $gateway();
                $success = $gateway->validate_order($row->orderid);

                mtrace($row->id . ' | ' . print_r($success, true)); // @codingStandardsIgnoreLine
            }
        } else {
            mtrace('No orders are waiting on a status update');
        }
        mtrace('-------------');
    }

    /**
     * Return all vat percentage that are possible
     *
     * @return array
     */
    public function get_vat_percentages(): array {
        return range(0, 99);
    }

}

/**
 * Needed for serving files.
 *
 * @param object $course
 * @param cm_info $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param bool $sendfileoptions
 *
 * @return void may terminate if file not found or donotdie not specified
 * @throws moodle_exception
 */
function enrol_coursepayment_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $sendfileoptions): void {
    global $CFG;
    require_once("$CFG->libdir/filelib.php");

    // No access control here.
    $options['cacheability'] = 'public';

    $fs = get_file_storage();
    $itemid = $args[0];
    $filename = $args[1];

    // Get the file.
    $file = $fs->get_file($context->id, 'enrol_coursepayment', $filearea, $itemid, '/', $filename);

    if ($file) {

        \core\session\manager::write_close(); // Unlock session during file serving.
        send_stored_file($file, null, 0, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * Serve the edit element as a fragment.
 *
 * @param array $args List of named arguments for the fragment loader.
 *
 * @return string
 * @throws dml_exception
 * @throws moodle_exception
 */
function enrol_coursepayment_output_fragment_editelement($args): string {
    global $DB;

    // Get the element.
    $element = $DB->get_record('coursepayment_elements', ['id' => $args['elementid']], '*', MUST_EXIST);

    $pageurl = new moodle_url('/enrol/coursepayment/view/invoice_rearrange.php', ['pid' => $element->pageid]);
    $form = new \enrol_coursepayment\invoice\edit_element_form($pageurl, ['element' => $element]);

    return $form->render();
}

/**
 * Handles editing the 'name' of the element in a list.
 *
 * @param string $itemtype
 * @param int $itemid
 * @param string $newvalue
 *
 * @return \core\output\inplace_editable
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 * @throws require_login_exception
 */
function enrol_coursepayment_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $PAGE;

    if ($itemtype === 'elementname') {
        $element = $DB->get_record('coursepayment_elements', ['id' => $itemid], '*', MUST_EXIST);
        $page = $DB->get_record('coursepayment_pages', ['id' => $element->pageid], '*', MUST_EXIST);
        $template = $DB->get_record('coursepayment_templates', ['id' => $page->templateid], '*', MUST_EXIST);

        // Set the template object.
        $template = new \enrol_coursepayment\invoice\template($template);
        // Perform checks.
        $PAGE->set_context(context_system::instance());
        require_login();

        // Make sure the user has the required capabilities.
        $template->require_manage();

        // Clean input and update the record.
        $updateelement = new stdClass();
        $updateelement->id = $element->id;
        $updateelement->name = clean_param($newvalue, PARAM_TEXT);
        $DB->update_record('coursepayment_elements', $updateelement);

        return new \core\output\inplace_editable('enrol_coursepayment', 'elementname', $element->id, true,
            $updateelement->name, $updateelement->name);
    }
}
