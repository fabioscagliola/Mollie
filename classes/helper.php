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
 * Helper
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2017 MFreak.nl
 * @author    Luuk Verhoeven
 */

/**
 * Class helper
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2017 MFreak.nl
 * @author    Luuk Verhoeven
 */
class enrol_coursepayment_helper {

    /**
     * Send a POST request to a remote location.
     *
     * @param string $url
     * @param array $data
     *
     * @return mixed
     */
    public static function post_request($url = '', $data = []) {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= $key . '=' . $value . '&';
        }
        rtrim($fields, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Get all available profile fields
     *
     * @return array
     * @throws dml_exception
     */
    public static function get_profile_fields(): array {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/profile/definelib.php');
        $rs = $DB->get_recordset_sql("SELECT f.* FROM {user_info_field} f ORDER BY name ASC");
        $fields = ['' => ''];
        foreach ($rs as $field) {
            $fields[$field->id] = $field->name;
        }
        $rs->close();
        if (empty($fields)) {
            return [];
        }

        return $fields;
    }

    /**
     * Get profile field data
     *
     * @param int $fieldid
     * @param int $userid
     *
     * @return string
     * @throws dml_exception
     */
    public static function get_profile_field_data($fieldid, $userid): string {
        global $DB;

        if (empty($fieldid)) {
            return '';
        }

        $field = $DB->get_record('user_info_field', ['id' => $fieldid], '*', MUST_EXIST);

        // Single user mode.
        $row = $DB->get_record('user_info_data', [
            'fieldid' => $field->id,
            'userid' => $userid,
        ]);
        if (isset($row->data)) {
            return $row->data;
        }

        if (isset($field->defaultdata)) {
            return $field->defaultdata;
        }

        return '';
    }

    /**
     * Get cmid info
     *
     * @param int $cmid
     * @param int $courseid
     *
     * @return bool|cm_info
     * @throws moodle_exception
     */
    public static function get_cmid_info($cmid = 0, $courseid = 0) {

        $modinfo = get_fast_modinfo($courseid);
        foreach ($modinfo->sections as $sectionnum => $section) {
            foreach ($section as $coursemoduleid) {
                if ($coursemoduleid == $cmid) {
                    return $modinfo->cms[$coursemoduleid];
                }
            }
        }

        return false;
    }

    /**
     * Get section info
     *
     * @param int $sectionnumber
     * @param int $courseid
     *
     * @return stdClass
     * @throws dml_exception
     */
    public static function get_section_info($sectionnumber = 0, $courseid = 0): stdClass {
        global $DB;

        $section = $DB->get_record('course_sections', [
            'course' => $courseid,
            'section' => $sectionnumber,
        ], '*', MUST_EXIST);

        $courseformat = course_get_format($courseid);
        $defaultsectionname = $courseformat->get_default_section_name($section);

        $module = new stdClass();
        $module->name = $defaultsectionname;

        return $module;
    }

    /**
     * Parse text
     *
     * @param string $text
     * @param stdClass $obj
     *
     * @return mixed|string
     */
    public static function parse_text($text, stdClass $obj) {
        if (preg_match_all('/\{+\w+\}/', $text, $matches)) {

            foreach ($matches[0] as $match) {

                $matchclean = str_replace(['{', '}'], '', $match);

                if (property_exists($obj, $matchclean)) {
                    $text = str_replace($match, $obj->$matchclean, $text);
                }
            }
        }

        return $text;
    }

    /**
     * edit_invoice_pdf_button
     *
     * @param int $tid
     *
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public static function get_edit_invoice_pdf_button(int $tid = 1): string {
        return '<br>' . html_writer::link(new moodle_url('/enrol/coursepayment/view/invoice_edit.php',
                [
                    'tid' => $tid,
                ]),
                get_string('btn:pdf_edit', 'enrol_coursepayment'), ['class' => 'btn btn-primary']);
    }

    /**
     * Get event object
     *
     * With fallback for older Moodle version
     *
     * @return \core\message\message|stdClass
     */
    public static function get_event_object() {
        global $CFG;
        if ($CFG->branch >= 29) {
            return new \core\message\message();
        }

        return new stdClass();
    }

    /**
     * Requires Mollie connect
     * Check are based only on config checks
     *
     * @return bool
     * @throws dml_exception
     */
    public static function requires_mollie_connect(): bool {
        $mollieconnect = get_config('enrol_coursepayment', 'mollieconnect');
        $accepted = get_config('enrol_coursepayment', 'mollie_connect_accepted');

        // Check local_mollieconnect, are we linked to Avetica?
        // This is only needed for installation after 2020-01-16.
        if (!empty($mollieconnect) && empty($accepted)) {
            // TODO debug.
            set_config('mollie_connect_accepted', 1, 'enrol_coursepayment');
        }

        return false;
    }

    /**
     * Get Mollie connect link
     *
     * @return string
     * @throws coding_exception
     */
    public static function get_mollie_connect_link(): string {
        global $CFG;

        return '<div class="alert alert-info">
                    ' . get_string('mollieconnect', 'enrol_coursepayment') . '
                    <a href="https://moodle.avetica.nl/local/mollieconnect/connector.php?link=' . urlencode($CFG->wwwroot) . '">
                         <img src="/enrol/coursepayment/pix/mollieconnect.png" alt="Mollie connect"/>
                    </a>
                </div>';
    }

    /**
     * Moodle 4.4 forward the welcome to course text contains links <a> that need to be stripped.
     * However, the strip_links function does not work as expected, as it has issues with the {$a} placeholders.
     * This function does get them stripped.
     *
     * @param string $string
     *
     * @return false|string
     */
    public static function strip_welcome_to_course_text($string) {
        // Replace line breaks with a placeholder.
        $string = str_replace(["\r\n", "\r", "\n"], '<br />', $string);

        $dom = new DOMDocument;
        $dom->loadHTML($string, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $links = $dom->getElementsByTagName('a');

        // Loop through all <a> tags in reverse order.
        for ($i = $links->length - 1; $i >= 0; $i--) {
            $link = $links->item($i);

            // Replace the link with its text content.
            $text = $dom->createTextNode($link->textContent);
            $link->parentNode->replaceChild($text, $link);
        }

        $html = $dom->saveHTML();

        // Restore line breaks.
        return str_replace('<br />', "\n", $html);
    }

}
