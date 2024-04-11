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
 * Template class
 *
 * this part is copied from "mod_customcert" - Mark Nelson <markn@moodle.com>
 * Thanks for allowing us to use it.
 *
 * This file is modified not compatible with the original.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 */

namespace enrol_coursepayment\invoice;

use context_system;
use enrol_coursepayment_helper;

/**
 * Class represents a coursepayment invoice template.
 */
class template {

    /**
     * @var int $id The id of the template.
     */
    protected int $id;

    /**
     * @var string $name The name of this template
     */
    protected string $name;

    /**
     * @var int $contextid The context id of this template
     */
    protected int $contextid;

    /**
     * The constructor.
     *
     * @param \stdClass $template
     */
    public function __construct(\stdClass $template) {
        $this->id = $template->id;
        $this->name = $template->name;
        $this->contextid = $template->contextid;
    }

    /**
     * Render a invoice.
     *
     * @param \stdClass $coursepayment
     * @param \stdClass $user
     * @param \stdClass $pluginconfig
     * @param mixed $a
     *
     * @return \stored_file
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \file_exception
     * @throws \stored_file_creation_exception
     */
    public static function render(\stdClass $coursepayment, \stdClass $user, \stdClass $pluginconfig, $a): \stored_file {
        global $DB;
        ob_clean();

        $template = $DB->get_record('coursepayment_templates', [
            'name' => self::get_template_name($pluginconfig, $user),
        ]);

        // Render the pdf.
        $template = new self($template);
        $pdfdata = $template->generate_pdf(false, $user, [
            'coursepayment' => $coursepayment,
            'pluginconfig' => $pluginconfig,
            'a' => $a,
        ], true);

        $fs = get_file_storage();
        $systemcontext = context_system::instance();

        // Cleanup previous builds.
        $fs->delete_area_files($systemcontext->id, 'enrol_coursepayment', 'invoice', $coursepayment->id);

        return $fs->create_file_from_string((object) [
            'contextid' => $systemcontext->id,
            'component' => 'enrol_coursepayment',
            'filearea' => 'invoice',
            'itemid' => $coursepayment->id,
            'userid' => $user->id,
            'filepath' => '/',
            'filename' => $coursepayment->id . '.pdf',
            'source' => 'invoice',
        ], $pdfdata);
    }

    /**
     * Get the template name base on multi-account setup.
     *
     * @param \stdClass $pluginconfig
     * @param \stdClass $user
     *
     * @return int
     * @throws \dml_exception
     */
    public static function get_template_name(\stdClass $pluginconfig, \stdClass $user): int {
        global $DB;
        $profilevalue = '';

        // Get correct invoice details check if the plugin uses multi-account.
        if (!empty($pluginconfig->multi_account_fieldid)) {
            $profilevalue = enrol_coursepayment_helper::get_profile_field_data($pluginconfig->multi_account_fieldid, $user->id);
        }

        // Prevent checking multi-account if this is disabled.
        if (empty($pluginconfig->multi_account)) {
            return 0;
        }

        // Load default multi-account.
        $multiaccount = $DB->get_record('coursepayment_multiaccount', [
            'is_default' => 1,
        ], '*');

        if (!empty($profilevalue)) {

            // Check if we have a multi-account matching your value.
            $mutiaccountstudent = $DB->get_record('coursepayment_multiaccount', [
                'profile_value' => $profilevalue,
            ], '*');

            // Found we should use this.
            if (!empty($mutiaccountstudent)) {
                return $mutiaccountstudent->id;
            }
        }

        return empty($multiaccount) ? 0 : $multiaccount->id;
    }

    /**
     * Set a default template
     *
     * @param int $name
     *
     * @throws \dml_exception
     */
    public static function install_default_template(int $name = 0): void {
        global $DB;

        // Todo shouldn't be run multiple times, better add a record exists.

        $contextid = context_system::instance()->id;

        $template = self::create($name, $contextid);

        // Create a page for this template.
        $pageid = $template->add_page();

        $elements = [
            [
                "pageid" => $pageid,
                "name" => "Orderdata",
                "element" => "orderdata",
                "data" => "",
                "font" => "times",
                "fontsize" => 12,
                "colour" => "#000000",
                "posx" => 8,
                "posy" => 101,
                "width" => 0,
                "refpoint" => 0,
                "sequence" => 2,
            ],
            [
                "id" => 21,
                "pageid" => $pageid,
                "name" => "Invoice information",
                "element" => "invoiceinfo",
                "data" => "",
                "font" => "times",
                "fontsize" => 12,
                "colour" => "#000000",
                "posx" => 8,
                "posy" => 6,
                "width" => 0,
                "refpoint" => 0,
                "sequence" => 3,
            ],
            [
                "id" => 22,
                "pageid" => $pageid,
                "name" => "Course category",
                "element" => "categoryname",
                "data" => "",
                "font" => "times",
                "fontsize" => 12,
                "colour" => "#000000",
                "posx" => 142,
                "posy" => 6,
                "width" => 0,
                "refpoint" => 0,
                "sequence" => 4,
            ],
            [
                "id" => 23,
                "pageid" => $pageid,
                "name" => "To:",
                "element" => "text",
                "data" => "To:",
                "font" => "times",
                "fontsize" => 12,
                "colour" => "#000000",
                "posx" => 8,
                "posy" => 70,
                "width" => 0,
                "refpoint" => 0,
                "sequence" => 5,
            ],
            [
                "id" => 25,
                "pageid" => $pageid,
                "name" => "Studentname",
                "element" => "studentname",
                "data" => "",
                "font" => "times",
                "fontsize" => 12,
                "colour" => "#000000",
                "posx" => 8,
                "posy" => 76,
                "width" => 0,
                "refpoint" => 0,
                "sequence" => 6,
            ],
            [
                "pageid" => $pageid,
                "name" => "Email",
                "element" => "userfield",
                "data" => "email",
                "font" => "times",
                "fontsize" => 12,
                "colour" => "#000000",
                "posx" => 8,
                "posy" => 82,
                "width" => 0,
                "refpoint" => 0,
                "sequence" => 7,
            ],
        ];

        // Adding the default elements.
        foreach ($elements as $element) {
            $DB->insert_record('coursepayment_elements', (object) $element);
        }
    }

    /**
     * Handles saving data.
     *
     * @param \stdClass $data the template data
     *
     * @return void
     * @throws \dml_exception
     */
    public function save(\stdClass $data): void {
        global $DB;

        $savedata = new \stdClass();
        $savedata->id = $this->id;
        $savedata->timemodified = time();
        $DB->update_record('coursepayment_templates', $savedata);
    }

    /**
     * Handles adding another page to the template.
     *
     * @return int the id of the page
     * @throws \dml_exception
     */
    public function add_page(): int {
        global $DB;

        // Set the page number to 1 to begin with.
        $sequence = 1;
        // Get the max page number.
        $sql = "SELECT MAX(sequence) as maxpage
                  FROM {coursepayment_pages} cp
                 WHERE cp.templateid = :templateid";
        if ($maxpage = $DB->get_record_sql($sql, ['templateid' => $this->id])) {
            $sequence = $maxpage->maxpage + 1;
        }

        // New page creation.
        $page = new \stdClass();
        $page->templateid = $this->id;
        $page->width = '210';
        $page->height = '297';
        $page->sequence = $sequence;
        $page->timecreated = time();
        $page->timemodified = $page->timecreated;

        // Insert the page.
        return $DB->insert_record('coursepayment_pages', $page);
    }

    /**
     * Handles saving page data.
     *
     * @param \stdClass $data the template data
     *
     * @throws \dml_exception
     */
    public function save_page(\stdClass $data): void {
        global $DB;

        // Set the time to a variable.
        $time = time();

        // Get the existing pages and save the page data.
        if ($pages = $DB->get_records('coursepayment_pages', ['templateid' => $data->tid])) {
            // Loop through existing pages.
            foreach ($pages as $page) {
                // Get the name of the fields we want from the form.
                $width = 'pagewidth_' . $page->id;
                $height = 'pageheight_' . $page->id;
                $leftmargin = 'pageleftmargin_' . $page->id;
                $rightmargin = 'pagerightmargin_' . $page->id;
                // Create the page data to update the DB with.
                $p = new \stdClass();
                $p->id = $page->id;
                $p->width = $data->$width;
                $p->height = $data->$height;
                $p->leftmargin = $data->$leftmargin;
                $p->rightmargin = $data->$rightmargin;
                $p->timemodified = $time;
                // Update the page.
                $DB->update_record('coursepayment_pages', $p);
            }
        }
    }

    /**
     * Handles deleting the template.
     *
     * @return bool return true if the deletion was successful, false otherwise
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function delete(): bool {
        global $DB;

        // Delete the elements.
        $sql = "SELECT e.*
                  FROM {coursepayment_elements} e
            INNER JOIN {coursepayment_pages} p
                    ON e.pageid = p.id
                 WHERE p.templateid = :templateid";
        if ($elements = $DB->get_records_sql($sql, ['templateid' => $this->id])) {
            foreach ($elements as $element) {
                // Get an instance of the element class.
                if ($e = element_factory::get_element_instance($element)) {
                    $e->delete();
                } else {
                    // The plugin files are missing, so just remove the entry from the DB.
                    $DB->delete_records('coursepayment_elements', ['id' => $element->id]);
                }
            }
        }

        // Delete the pages.
        if (!$DB->delete_records('coursepayment_pages', ['templateid' => $this->id])) {
            return false;
        }

        // Now, finally delete the actual template.
        if (!$DB->delete_records('coursepayment_templates', ['id' => $this->id])) {
            return false;
        }

        return true;
    }

    /**
     * Handles deleting a page from the template.
     *
     * @param int $pageid the template page
     *
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public function delete_page(int $pageid): void {
        global $DB;

        // Get the page.
        $page = $DB->get_record('coursepayment_pages', ['id' => $pageid], '*', MUST_EXIST);

        // Delete this page.
        $DB->delete_records('coursepayment_pages', ['id' => $page->id]);

        // The element may have some extra tasks it needs to complete to completely delete itself.
        if ($elements = $DB->get_records('coursepayment_elements', ['pageid' => $page->id])) {
            foreach ($elements as $element) {
                // Get an instance of the element class.
                if ($e = element_factory::get_element_instance($element)) {
                    $e->delete();
                } else {
                    // The plugin files are missing, so just remove the entry from the DB.
                    $DB->delete_records('coursepayment_elements', ['id' => $element->id]);
                }
            }
        }

        // Now we want to decrease the page number values of
        // the pages that are greater than the page we deleted.
        $sql = "UPDATE {coursepayment_pages}
                   SET sequence = sequence - 1
                 WHERE templateid = :templateid
                   AND sequence > :sequence";
        $DB->execute($sql, ['templateid' => $this->id, 'sequence' => $page->sequence]);
    }

    /**
     * Handles deleting an element from the template.
     *
     * @param int $elementid the template page
     *
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public function delete_element(int $elementid): void {
        global $DB;

        // Ensure element exists and delete it.
        $element = $DB->get_record('coursepayment_elements', ['id' => $elementid], '*', MUST_EXIST);

        // Get an instance of the element class.
        if ($e = element_factory::get_element_instance($element)) {
            $e->delete();
        } else {
            // The plugin files are missing, so just remove the entry from the DB.
            $DB->delete_records('coursepayment_elements', ['id' => $elementid]);
        }

        // Now we want to decrease the sequence numbers of the elements
        // that are greater than the element we deleted.
        $sql = "UPDATE {coursepayment_elements}
                   SET sequence = sequence - 1
                 WHERE pageid = :pageid
                   AND sequence > :sequence";
        $DB->execute($sql, ['pageid' => $element->pageid, 'sequence' => $element->sequence]);
    }

    /**
     * Generate the PDF for the template.
     *
     * @param bool $preview  true if it is a preview, false otherwise
     * @param int|null $user the user object
     * @param array $data
     * @param bool $return   Do we want to return the contents of the PDF?
     *
     * @return string|void Can return the PDF in string format if specified.
     */
    public function generate_pdf(bool $preview = false, ?int $user = null, array $data = [], bool $return = false) {
        global $CFG, $DB, $USER;
        require_once($CFG->libdir . '/pdflib.php');

        if (empty($user)) {
            $user = $USER;
        }

        // Get the pages for the template, there should always be at least one page for each template.
        if ($pages = $DB->get_records('coursepayment_pages', ['templateid' => $this->id], 'sequence ASC')) {
            // Create the pdf object.
            $pdf = new \pdf();

            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetTitle($this->name);
            $pdf->SetAutoPageBreak(true, 0);
            // Remove full-stop at the end, if it exists, to avoid "..pdf" being created and being filtered by clean_filename.
            $filename = rtrim($this->name, '.');
            $filename = clean_filename($filename . '.pdf');
            // Loop through the pages and display their content.
            foreach ($pages as $page) {
                // Add the page to the PDF.
                if ($page->width > $page->height) {
                    $orientation = 'L';
                } else {
                    $orientation = 'P';
                }
                $pdf->AddPage($orientation, [$page->width, $page->height]);
                $pdf->SetMargins($page->leftmargin, 0, $page->rightmargin);
                // Get the elements for the page.
                if ($elements = $DB->get_records('coursepayment_elements', ['pageid' => $page->id], 'sequence ASC')) {
                    // Loop through and display.
                    foreach ($elements as $element) {
                        // Get an instance of the element class.
                        if ($e = element_factory::get_element_instance($element)) {
                            $e->render($pdf, $preview, $user, $data);
                        }
                    }
                }
            }
            if ($return) {
                return $pdf->Output('', 'S');
            }

            $pdf->Output($filename, 'I');
        }
    }

    /**
     * Handles copying this template into another.
     *
     * @param int $copytotemplateid The template id to copy to
     *
     * @return void
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public function copy_to_template($copytotemplateid): void {
        global $DB;

        // Get the pages for the template, there should always be at least one page for each template.
        if ($templatepages = $DB->get_records('coursepayment_pages', ['templateid' => $this->id])) {
            // Loop through the pages.
            foreach ($templatepages as $templatepage) {
                $page = clone($templatepage);
                $page->templateid = $copytotemplateid;
                $page->timecreated = time();
                $page->timemodified = $page->timecreated;
                // Insert into the database.
                $page->id = $DB->insert_record('coursepayment_pages', $page);
                // Now go through the elements we want to load.
                if ($templateelements = $DB->get_records('coursepayment_elements', ['pageid' => $templatepage->id])) {
                    foreach ($templateelements as $templateelement) {
                        $element = clone($templateelement);
                        $element->pageid = $page->id;
                        $element->timecreated = time();
                        $element->timemodified = $element->timecreated;
                        // Ok, now we want to insert this into the database.
                        $element->id = $DB->insert_record('coursepayment_elements', $element);
                        // Load any other information the element may need to for the template.
                        if ($e = element_factory::get_element_instance($element)) {
                            if (!$e->copy_element($templateelement)) {
                                // Failed to copy - delete the element.
                                $e->delete();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Handles moving an item on a template.
     *
     * @param string $itemname  the item we are moving
     * @param int $itemid       the id of the item
     * @param string $direction the direction
     *
     * @return void
     * @throws \dml_exception
     */
    public function move_item(string $itemname, int $itemid, string $direction): void {
        global $DB;

        $table = 'coursepayment_';
        if ($itemname == 'page') {
            $table .= 'pages';
        } else { // Must be an element.
            $table .= 'elements';
        }

        if ($moveitem = $DB->get_record($table, ['id' => $itemid])) {
            // Check which direction we are going.
            if ($direction == 'up') {
                $sequence = $moveitem->sequence - 1;
            } else { // Must be down.
                $sequence = $moveitem->sequence + 1;
            }

            // Get the item we will be swapping with. Make sure it is related to the same template (if it's
            // a page) or the same page (if it's an element).
            if ($itemname == 'page') {
                $params = ['templateid' => $moveitem->templateid];
            } else { // Must be an element.
                $params = ['pageid' => $moveitem->pageid];
            }
            $swapitem = $DB->get_record($table, $params + ['sequence' => $sequence]);
        }

        // Check that there is an item to move, and an item to swap it with.
        if ($moveitem && !empty($swapitem)) {
            $DB->set_field($table, 'sequence', $swapitem->sequence, ['id' => $moveitem->id]);
            $DB->set_field($table, 'sequence', $moveitem->sequence, ['id' => $swapitem->id]);
        }
    }

    /**
     * Returns the id of the template.
     *
     * @return int the id of the template
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Returns the name of the template.
     *
     * @return string the name of the template
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Returns the context id.
     *
     * @return int the context id
     */
    public function get_contextid(): int {
        return $this->contextid;
    }

    /**
     * Returns the context id.
     *
     * @return \context the context
     * @throws \coding_exception
     */
    public function get_context(): \context {
        return \context::instance_by_id($this->contextid);
    }

    /**
     * Returns the context id.
     *
     * @return \context_module|null the context module, null if there is none
     * @throws \coding_exception
     */
    public function get_cm(): ?\context_module {
        $context = $this->get_context();
        if ($context->contextlevel === CONTEXT_MODULE) {
            return get_coursemodule_from_id('enrol_coursepayment', $context->instanceid, 0, false, MUST_EXIST);
        }

        return null;
    }

    /**
     * Ensures the user has the proper capabilities to manage this template.
     *
     * @throws \required_capability_exception if the user does not have the necessary capabilities (ie. Fred)
     * @throws \coding_exception
     */
    public function require_manage(): void {
        require_capability('enrol/coursepayment:manage', $this->get_context());
    }

    /**
     * Creates a template.
     *
     * @param string $templatename the name of the template
     * @param int $contextid       the context id
     *
     * @return template
     * @throws \dml_exception
     */
    public static function create(string $templatename, int $contextid): template {
        global $DB;

        $template = new \stdClass();
        $template->name = $templatename;
        $template->contextid = $contextid;
        $template->timecreated = time();
        $template->timemodified = $template->timecreated;
        $template->id = $DB->insert_record('coursepayment_templates', $template);

        return new self($template);
    }

}
