<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * This file contains the form for handling the layout of the coursepayment template instance.
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

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

global $CFG;
require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/enrol/coursepayment/classes/invoice/colourpicker.php');

\MoodleQuickForm::registerElementType('coursepayment_colourpicker',
    $CFG->dirroot . '/enrol/coursepayment/classes/invoice/colourpicker.php', 'colourpicker');

/**
 * The form for handling the layout of the customcert instance.
 *
 * @package    enrol_coursepayment
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_form extends \moodleform {

    /**
     * @var int|null The id of the template being used.
     */
    protected $tid = null;

    /**
     * @var int The total number of pages for this cert.
     */
    protected int $numpages = 1;

    /**
     * Form definition.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function definition(): void {
        global $DB, $OUTPUT;

        $mform =& $this->_form;

        // Get the number of pages for this module.
        if (isset($this->_customdata['tid'])) {
            $this->tid = $this->_customdata['tid'];
            if ($pages = $DB->get_records('coursepayment_pages', ['templateid' => $this->tid], 'sequence')) {
                $this->numpages = count($pages);
                foreach ($pages as $p) {
                    $this->add_coursepayment_page_elements($p);
                }
            }
        } else { // Add a new template.
            // Create a 'fake' page to display the elements on - not yet saved in the DB.
            $page = new \stdClass();
            $page->id = 0;
            $page->sequence = 1;
            $this->add_coursepayment_page_elements($page);
        }

        // Link to add another page, only display it when the template has been created.
        if (isset($this->_customdata['tid'])) {
            $addpagelink = new \moodle_url('/enrol/coursepayment/view/invoice_edit.php',
                [
                    'tid' => $this->tid,
                    'aid' => 1,
                    'action' => 'addpage',
                    'sesskey' => sesskey(),
                ]
            );
            $icon = $OUTPUT->pix_icon('t/switch_plus', get_string('addpage', 'enrol_coursepayment'));
            $addpagehtml = \html_writer::link($addpagelink, $icon . get_string('addpage', 'enrol_coursepayment'));
            $mform->addElement('html', \html_writer::tag('div', $addpagehtml, ['class' => 'addpage']));
        }

        // Add the submit buttons.
        $group = [];
        $group[] = $mform->createElement('submit', 'submitbtn', get_string('savechanges'));
        $group[] = $mform->createElement('submit', 'previewbtn',
            get_string('savechangespreview', 'enrol_coursepayment'), [], false);
        $mform->addElement('group', 'submitbtngroup', '', $group, '', false);

        $mform->addElement('hidden', 'tid');
        $mform->setType('tid', PARAM_INT);
        $mform->setDefault('tid', $this->tid);
    }

    /**
     * Fill in the current page data for this coursepayment.
     *
     * @return void
     * @throws \dml_exception
     */
    public function definition_after_data(): void {
        global $DB;

        $mform = $this->_form;

        // Check that we are updating a current customcert.
        if ($this->tid) {
            // Get the pages for this customcert.
            if ($pages = $DB->get_records('coursepayment_pages', ['templateid' => $this->tid])) {
                // Loop through the pages.
                foreach ($pages as $p) {
                    // Set the width.
                    $element = $mform->getElement('pagewidth_' . $p->id);
                    $element->setValue($p->width);
                    // Set the height.
                    $element = $mform->getElement('pageheight_' . $p->id);
                    $element->setValue($p->height);
                    // Set the left margin.
                    $element = $mform->getElement('pageleftmargin_' . $p->id);
                    $element->setValue($p->leftmargin);
                    // Set the right margin.
                    $element = $mform->getElement('pagerightmargin_' . $p->id);
                    $element->setValue($p->rightmargin);
                }
            }
        }
    }

    /**
     * Some basic validation.
     *
     * @param array $data
     * @param array $files
     *
     * @return array the errors that were found
     * @throws \coding_exception
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        // Go through the data and check any width, height or margin  values.
        foreach ($data as $key => $d) {

            $errors = $this->validate_width($data, $key, $errors);
            $errors = $this->validate_height($data, $key, $errors);

            $errors = $this->validate_left_margin($data, $key, $errors);
            $errors = $this->validate_right_margin($data, $key, $errors);
        }

        return $errors;
    }

    /**
     * Adds the page elements to the form.
     *
     * @param \stdClass $page the coursepayment page
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    protected function add_coursepayment_page_elements(\stdClass $page): void {
        global $DB, $OUTPUT;

        // Create the form object.
        $mform =& $this->_form;

        if ($this->numpages > 1) {
            $mform->addElement('header', 'page_' . $page->id,
                get_string('page', 'enrol_coursepayment', $page->sequence));
        }

        $editlink = '/enrol/coursepayment/view/invoice_edit.php';
        $editlinkparams = ['tid' => $this->tid, 'sesskey' => sesskey()];
        $editelementlink = '/enrol/coursepayment/view/invoice_edit_element.php';

        // Place the ordering arrows.
        // Only display the move up arrow if it is not the first.
        if ($page->sequence > 1) {
            $url = new \moodle_url($editlink, $editlinkparams + ['action' => 'pmoveup', 'aid' => $page->id]);
            $mform->addElement('html', $OUTPUT->action_icon($url, new \pix_icon('t/up',
                get_string('moveup'))));
        }
        // Only display the move down arrow if it is not the last.
        if ($page->sequence < $this->numpages) {
            $url = new \moodle_url($editlink, $editlinkparams + ['action' => 'pmovedown', 'aid' => $page->id]);
            $mform->addElement('html', $OUTPUT->action_icon($url, new \pix_icon('t/down',
                get_string('movedown'))));
        }

        $mform->addElement('text', 'pagewidth_' . $page->id, get_string('width', 'enrol_coursepayment'));
        $mform->setType('pagewidth_' . $page->id, PARAM_INT);
        $mform->setDefault('pagewidth_' . $page->id, '210');
        $mform->addRule('pagewidth_' . $page->id, null, 'required', null, 'client');
        $mform->addHelpButton('pagewidth_' . $page->id, 'width', 'enrol_coursepayment');

        $mform->addElement('text', 'pageheight_' . $page->id, get_string('height',
            'enrol_coursepayment'));
        $mform->setType('pageheight_' . $page->id, PARAM_INT);
        $mform->setDefault('pageheight_' . $page->id, '297');
        $mform->addRule('pageheight_' . $page->id, null, 'required', null, 'client');
        $mform->addHelpButton('pageheight_' . $page->id, 'height', 'enrol_coursepayment');

        $mform->addElement('text', 'pageleftmargin_' . $page->id, get_string('leftmargin',
            'enrol_coursepayment'));
        $mform->setType('pageleftmargin_' . $page->id, PARAM_INT);
        $mform->setDefault('pageleftmargin_' . $page->id, 0);
        $mform->addHelpButton('pageleftmargin_' . $page->id, 'leftmargin', 'enrol_coursepayment');

        $mform->addElement('text', 'pagerightmargin_' . $page->id, get_string('rightmargin',
            'enrol_coursepayment'));
        $mform->setType('pagerightmargin_' . $page->id, PARAM_INT);
        $mform->setDefault('pagerightmargin_' . $page->id, 0);
        $mform->addHelpButton('pagerightmargin_' . $page->id, 'rightmargin', 'enrol_coursepayment');

        // Check if there are elements to add.
        if ($elements = $DB->get_records('coursepayment_elements', ['pageid' => $page->id], 'sequence ASC')) {
            // Get the total number of elements.
            $numelements = count($elements);
            // Create a table to display these elements.
            $table = new \html_table();
            $table->attributes = ['class' => 'generaltable elementstable'];
            $table->head = [
                get_string('name', 'enrol_coursepayment'),
                get_string('type',
                    'enrol_coursepayment'),
                '',
            ];
            $table->align = ['left', 'left', 'left'];
            // Loop through and add the elements to the table.
            foreach ($elements as $element) {
                $elementname = new \core\output\inplace_editable('enrol_coursepayment',
                    'elementname', $element->id,
                    true, format_string($element->name), $element->name);

                $row = new \html_table_row();
                $row->cells[] = $OUTPUT->render($elementname);
                $row->cells[] = $element->element;
                // Link to edit this element.
                $link = new \moodle_url($editelementlink, [
                        'tid' => $this->tid,
                        'sesskey' => sesskey(),
                    ] + [
                        'id' => $element->id,
                        'action' => 'edit',
                    ]);
                $icons = $OUTPUT->action_icon($link, new \pix_icon('t/edit', get_string('edit')), null,
                    ['class' => 'action-icon edit-icon']);
                // Link to delete the element.
                $link = new \moodle_url($editlink, $editlinkparams + [
                        'action' => 'deleteelement',
                        'aid' => $element->id,
                    ]);
                $icons .= $OUTPUT->action_icon($link, new \pix_icon('t/delete', get_string('delete')), null,
                    ['class' => 'action-icon delete-icon']);
                // Now display any moving arrows if they are needed.
                if ($numelements > 1) {
                    // Only display the move up arrow if it is not the first.
                    $moveicons = '';
                    if ($element->sequence > 1) {
                        $url = new \moodle_url($editlink, $editlinkparams + [
                                'action' => 'emoveup',
                                'aid' => $element->id,
                            ]);
                        $moveicons .= $OUTPUT->action_icon($url, new \pix_icon('t/up', get_string('moveup')));
                    }
                    // Only display the move down arrow if it is not the last.
                    if ($element->sequence < $numelements) {
                        $url = new \moodle_url($editlink, $editlinkparams + [
                                'action' => 'emovedown',
                                'aid' => $element->id,
                            ]);
                        $moveicons .= $OUTPUT->action_icon($url, new \pix_icon('t/down', get_string('movedown')));
                    }
                    $icons .= $moveicons;
                }
                $row->cells[] = $icons;
                $table->data[] = $row;
            }
            // Create link to order the elements.
            $link = \html_writer::link(new \moodle_url('/enrol/coursepayment/view/invoice_rearrange.php', ['pid' => $page->id]),
                get_string('rearrangeelements', 'enrol_coursepayment'));
            // Add the table to the form.
            $mform->addElement('static', 'elements_' . $page->id, get_string('elements',
                'enrol_coursepayment'), \html_writer::table($table)
                . \html_writer::tag('div', $link));
            $mform->addHelpButton('elements_' . $page->id, 'elements', 'enrol_coursepayment');
        }

        $group = [];
        $group[] = $mform->createElement('select', 'element_' . $page->id, '',
            element_helper::get_available_element_types());
        $group[] = $mform->createElement('submit', 'addelement_' . $page->id,
            get_string('addelement', 'enrol_coursepayment'),
            [], false);
        $mform->addElement('group', 'elementgroup', '', $group, '', false);

        // Add option to delete this page if there is more than one page.
        if ($this->numpages > 1) {
            // Link to delete the page.
            $deletelink = new \moodle_url($editlink, $editlinkparams + ['action' => 'deletepage', 'aid' => $page->id]);
            $icon = $OUTPUT->pix_icon('t/delete', get_string('deletepage', 'enrol_coursepayment'));
            $deletepagehtml = \html_writer::link($deletelink,
                $icon . get_string('deletepage', 'enrol_coursepayment'));
            $mform->addElement('html', \html_writer::tag('div', $deletepagehtml, ['class' => 'deletebutton']));
        }
    }

    /**
     * Validate width
     *
     * @param array $data
     * @param string $key
     * @param array $errors
     *
     * @return array
     * @throws \coding_exception
     */
    protected function validate_width($data, $key, $errors): array {
        if (strpos($key, 'pagewidth_') !== false) {
            $page = str_replace('pagewidth_', '', $key);
            $widthid = 'pagewidth_' . $page;
            // Validate that the width is a valid value.
            if ((!isset($data[$widthid])) || (!is_numeric($data[$widthid])) || ($data[$widthid] <= 0)) {
                $errors[$widthid] = get_string('invalidwidth', 'enrol_coursepayment');
            }
        }

        return $errors;
    }

    /**
     * Validate height
     *
     * @param array $data
     * @param string $key
     * @param array $errors
     *
     * @return array
     * @throws \coding_exception
     */
    protected function validate_height($data, $key, $errors): array {
        if (strpos($key, 'pageheight_') !== false) {
            $page = str_replace('pageheight_', '', $key);
            $heightid = 'pageheight_' . $page;
            // Validate that the height is a valid value.
            if ((!isset($data[$heightid])) || (!is_numeric($data[$heightid])) || ($data[$heightid] <= 0)) {
                $errors[$heightid] = get_string('invalidheight', 'enrol_coursepayment');
            }
        }

        return $errors;
    }

    /**
     * Validate left margin
     *
     * @param array $data
     * @param string $key
     * @param array $errors
     *
     * @return array
     * @throws \coding_exception
     */
    protected function validate_left_margin($data, $key, $errors): array {
        if (strpos($key, 'pageleftmargin_') !== false) {
            // Validate that the left margin is a valid value.
            if (isset($data[$key]) && ($data[$key] < 0)) {
                $errors[$key] = get_string('invalidmargin', 'enrol_coursepayment');
            }
        }

        return $errors;
    }

    /**
     * Validate right margin
     *
     * @param array $data
     * @param string $key
     * @param array $errors
     *
     * @return array
     * @throws \coding_exception
     */
    protected function validate_right_margin($data, $key, $errors): array {
        if (strpos($key, 'pagerightmargin_') !== false) {
            // Validate that the right margin is a valid value.
            if (isset($data[$key]) && ($data[$key] < 0)) {
                $errors[$key] = get_string('invalidmargin', 'enrol_coursepayment');
            }
        }

        return $errors;
    }

}
