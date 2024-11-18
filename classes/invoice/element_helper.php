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
 * Template helper class
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

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/grade/constants.php');
require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/grade/querylib.php');

/**
 * Class helper.
 */
class element_helper {

    /**
     * @var int the top-left of element
     */
    public const COURSEPAYMENT_REF_POINT_TOPLEFT = 0;

    /**
     * @var int the top-center of element
     */
    public const COURSEPAYMENT_REF_POINT_TOPCENTER = 1;

    /**
     * @var int the top-left of element
     */
    public const COURSEPAYMENT_REF_POINT_TOPRIGHT = 2;

    /**
     * Common behaviour for rendering specified content on the pdf.
     *
     * @param \pdf $pdf        the pdf object
     * @param element $element the coursepayment invoice element
     * @param string $content  the content to render
     *
     * @return void
     */
    public static function render_content($pdf, $element, $content): void {
        [$font, $attr] = self::get_font($element);
        $pdf->setFont($font, $attr, $element->get_fontsize());
        $fontcolour = \TCPDF_COLORS::convertHTMLColorToDec($element->get_colour(), $fontcolour);
        $pdf->SetTextColor($fontcolour['R'], $fontcolour['G'], $fontcolour['B']);

        $x = $element->get_posx();
        $y = $element->get_posy();
        $w = $element->get_width();
        $refpoint = $element->get_refpoint();
        $actualwidth = $pdf->GetStringWidth($content);

        if ($w && $w < $actualwidth) {
            $actualwidth = $w;
        }

        switch ($refpoint) {
            case self::COURSEPAYMENT_REF_POINT_TOPRIGHT:
                $x = $element->get_posx() - $actualwidth;
                if ($x < 0) {
                    $x = 0;
                    $w = $element->get_posx();
                } else {
                    $w = $actualwidth;
                }
                break;
            case self::COURSEPAYMENT_REF_POINT_TOPCENTER:
                $x = $element->get_posx() - $actualwidth / 2;
                if ($x < 0) {
                    $x = 0;
                    $w = $element->get_posx() * 2;
                } else {
                    $w = $actualwidth;
                }
                break;
        }

        if ($w) {
            $w += 0.0001;
        }
        $pdf->setCellPaddings(0, 0, 0, 0);
        $pdf->writeHTMLCell($w, 0, $x, $y, $content, 0, 0, false, true);
    }

    /**
     * Common behaviour for rendering specified content on the drag and drop page.
     *
     * @param element $element the customcert element
     * @param string $content  the content to render
     *
     * @return string the html
     */
    public static function render_html_content(element $element, string $content): string {
        [$font, $attr] = self::get_font($element);
        $fontstyle = 'font-family: ' . $font;
        if (strpos($attr, 'B') !== false) {
            $fontstyle .= '; font-weight: bold';
        }
        if (strpos($attr, 'I') !== false) {
            $fontstyle .= '; font-style: italic';
        }

        $style = $fontstyle . '; color: ' . $element->get_colour() . '; font-size: ' . $element->get_fontsize() . 'pt;';
        if ($element->get_width()) {
            $style .= ' width: ' . $element->get_width() . 'mm';
        }

        return \html_writer::div($content, '', ['style' => $style]);
    }

    /**
     * Helper function to render the font elements.
     *
     * @param edit_element_form $mform the edit_form instance.
     *
     * @throws \coding_exception
     */
    public static function render_form_element_font($mform): void {
        $mform->addElement('select', 'font', get_string('font', 'enrol_coursepayment'), helper::get_fonts());
        $mform->setType('font', PARAM_TEXT);
        $mform->setDefault('font', 'times');
        $mform->addHelpButton('font', 'font', 'enrol_coursepayment');
        $mform->addElement('select', 'fontsize', get_string('fontsize', 'enrol_coursepayment'),
            helper::get_font_sizes());
        $mform->setType('fontsize', PARAM_INT);
        $mform->setDefault('fontsize', 12);
        $mform->addHelpButton('fontsize', 'fontsize', 'enrol_coursepayment');
    }

    /**
     * Helper function to render the colour elements.
     *
     * @param edit_element_form $mform the edit_form instance.
     *
     * @throws \coding_exception
     */
    public static function render_form_element_colour($mform): void {
        $mform->addElement('coursepayment_colourpicker', 'colour', get_string('fontcolour', 'enrol_coursepayment'));
        $mform->setType('colour', PARAM_RAW); // Need to validate that this is a valid colour.
        $mform->setDefault('colour', '#000000');
        $mform->addHelpButton('colour', 'fontcolour', 'enrol_coursepayment');
    }

    /**
     * Helper function to render the position elements.
     *
     * @param edit_element_form $mform the edit_form instance.
     *
     * @throws \coding_exception
     */
    public static function render_form_element_position($mform): void {
        $mform->addElement('text', 'posx', get_string('posx', 'enrol_coursepayment'), ['size' => 10]);
        $mform->setType('posx', PARAM_INT);
        $mform->setDefault('posx', 0);
        $mform->addHelpButton('posx', 'posx', 'enrol_coursepayment');
        $mform->addElement('text', 'posy', get_string('posy', 'enrol_coursepayment'), ['size' => 10]);
        $mform->setType('posy', PARAM_INT);
        $mform->setDefault('posy', 0);
        $mform->addHelpButton('posy', 'posy', 'enrol_coursepayment');
    }

    /**
     * Helper function to render the width element.
     *
     * @param edit_element_form $mform the edit_form instance.
     *
     * @throws \coding_exception
     */
    public static function render_form_element_width($mform): void {
        $mform->addElement('text', 'width', get_string('elementwidth', 'enrol_coursepayment'), ['size' => 10]);
        $mform->setType('width', PARAM_INT);
        $mform->setDefault('width', 0);
        $mform->addHelpButton('width', 'elementwidth', 'enrol_coursepayment');
        $refpointoptions = [];
        $refpointoptions[self::COURSEPAYMENT_REF_POINT_TOPLEFT] = get_string('topleft', 'enrol_coursepayment');
        $refpointoptions[self::COURSEPAYMENT_REF_POINT_TOPCENTER] = get_string('topcenter', 'enrol_coursepayment');
        $refpointoptions[self::COURSEPAYMENT_REF_POINT_TOPRIGHT] = get_string('topright', 'enrol_coursepayment');
        $mform->addElement('select', 'refpoint', get_string('refpoint', 'enrol_coursepayment'), $refpointoptions);
        $mform->setType('refpoint', PARAM_INT);
        $mform->setDefault('refpoint', self::COURSEPAYMENT_REF_POINT_TOPLEFT);
        $mform->addHelpButton('refpoint', 'refpoint', 'enrol_coursepayment');
    }

    /**
     * Helper function to performs validation on the colour element.
     *
     * @param array $data the submitted data
     *
     * @return array the validation errors
     * @throws \coding_exception
     */
    public static function validate_form_element_colour(array $data): array {
        $errors = [];
        // Validate the colour.
        if (!self::validate_colour($data['colour'])) {
            $errors['colour'] = get_string('invalidcolour', 'enrol_coursepayment');
        }

        return $errors;
    }

    /**
     * Helper function to performs validation on the position elements.
     *
     * @param array $data the submitted data
     *
     * @return array the validation errors
     * @throws \coding_exception
     */
    public static function validate_form_element_position(array $data): array {
        $errors = [];

        // Check if posx is not set, or not numeric or less than 0.
        if ((!isset($data['posx'])) || (!is_numeric($data['posx'])) || ($data['posx'] < 0)) {
            $errors['posx'] = get_string('invalidposition', 'enrol_coursepayment', 'X');
        }
        // Check if posy is not set, or not numeric or less than 0.
        if ((!isset($data['posy'])) || (!is_numeric($data['posy'])) || ($data['posy'] < 0)) {
            $errors['posy'] = get_string('invalidposition', 'enrol_coursepayment', 'Y');
        }

        return $errors;
    }

    /**
     * Helper function to perform validation on the width element.
     *
     * @param array $data the submitted data
     *
     * @return array the validation errors
     * @throws \coding_exception
     */
    public static function validate_form_element_width(array $data): array {
        $errors = [];

        // Check if width is less than 0.
        if (isset($data['width']) && $data['width'] < 0) {
            $errors['width'] = get_string('invalidelementwidth', 'enrol_coursepayment');
        }

        return $errors;
    }

    /**
     * Returns the font used for this element.
     *
     * @param element $element the customcert element
     *
     * @return array the font and font attributes
     */
    public static function get_font(element $element): array {
        // Variable for the font.
        $font = $element->get_font();
        // Get the last two characters of the font name.
        $fontlength = strlen($font);
        $lastchar = $font[$fontlength - 1];
        $secondlastchar = $font[$fontlength - 2];
        // The attributes of the font.
        $attr = '';
        // Check if the last character is 'i'.
        if ($lastchar == 'i') {
            // Remove the 'i' from the font name.
            $font = substr($font, 0, -1);
            // Check if the second last char is b.
            if ($secondlastchar == 'b') {
                // Remove the 'b' from the font name.
                $font = substr($font, 0, -1);
                $attr .= 'B';
            }
            $attr .= 'I';
        } else if ($lastchar == 'b') {
            // Remove the 'b' from the font name.
            $font = substr($font, 0, -1);
            $attr .= 'B';
        }

        return [$font, $attr];
    }

    /**
     * Validates the colour selected.
     *
     * @param string $colour
     *
     * @return bool returns true if the colour is valid, false otherwise
     */
    public static function validate_colour($colour): bool {
        // List of valid HTML colour names.
        $colournames = [
            'aliceblue',
            'antiquewhite',
            'aqua',
            'aquamarine',
            'azure',
            'beige',
            'bisque',
            'black',
            'blanchedalmond',
            'blue',
            'blueviolet',
            'brown',
            'burlywood',
            'cadetblue',
            'chartreuse',
            'chocolate',
            'coral',
            'cornflowerblue',
            'cornsilk',
            'crimson',
            'cyan',
            'darkblue',
            'darkcyan',
            'darkgoldenrod',
            'darkgray',
            'darkgrey',
            'darkgreen',
            'darkkhaki',
            'darkmagenta',
            'darkolivegreen',
            'darkorange',
            'darkorchid',
            'darkred',
            'darksalmon',
            'darkseagreen',
            'darkslateblue',
            'darkslategray',
            'darkslategrey',
            'darkturquoise',
            'darkviolet',
            'deeppink',
            'deepskyblue',
            'dimgray',
            'dimgrey',
            'dodgerblue',
            'firebrick',
            'floralwhite',
            'forestgreen',
            'fuchsia',
            'gainsboro',
            'ghostwhite',
            'gold',
            'goldenrod',
            'gray',
            'grey',
            'green',
            'greenyellow',
            'honeydew',
            'hotpink',
            'indianred',
            'indigo',
            'ivory',
            'khaki',
            'lavender',
            'lavenderblush',
            'lawngreen',
            'lemonchiffon',
            'lightblue',
            'lightcoral',
            'lightcyan',
            'lightgoldenrodyellow',
            'lightgray',
            'lightgrey',
            'lightgreen',
            'lightpink',
            'lightsalmon',
            'lightseagreen',
            'lightskyblue',
            'lightslategray',
            'lightslategrey',
            'lightsteelblue',
            'lightyellow',
            'lime',
            'limegreen',
            'linen',
            'magenta',
            'maroon',
            'mediumaquamarine',
            'mediumblue',
            'mediumorchid',
            'mediumpurple',
            'mediumseagreen',
            'mediumslateblue',
            'mediumspringgreen',
            'mediumturquoise',
            'mediumvioletred',
            'midnightblue',
            'mintcream',
            'mistyrose',
            'moccasin',
            'navajowhite',
            'navy',
            'oldlace',
            'olive',
            'olivedrab',
            'orange',
            'orangered',
            'orchid',
            'palegoldenrod',
            'palegreen',
            'paleturquoise',
            'palevioletred',
            'papayawhip',
            'peachpuff',
            'peru',
            'pink',
            'plum',
            'powderblue',
            'purple',
            'red',
            'rosybrown',
            'royalblue',
            'saddlebrown',
            'salmon',
            'sandybrown',
            'seagreen',
            'seashell',
            'sienna',
            'silver',
            'skyblue',
            'slateblue',
            'slategray',
            'slategrey',
            'snow',
            'springgreen',
            'steelblue',
            'tan',
            'teal',
            'thistle',
            'tomato',
            'turquoise',
            'violet',
            'wheat',
            'white',
            'whitesmoke',
            'yellow',
            'yellowgreen',
        ];

        if (preg_match('/^#?([[:xdigit:]]{3}){1,2}$/', $colour)) {
            return true;
        } else if (in_array(strtolower($colour), $colournames)) {
            return true;
        }

        return false;
    }

    /**
     * Helper function that returns the sequence on a specified customcert page for a
     * newly created element.
     *
     * @param int $pageid the id of the page we are adding this element to
     *
     * @return int the element number
     * @throws \dml_exception
     */
    public static function get_element_sequence(int $pageid): int {
        global $DB;

        // Set the sequence of the element we are creating.
        $sequence = 1;
        // Check if there already elements that exist, if so, overwrite value.
        $sql = "SELECT MAX(sequence) as maxsequence
                  FROM {coursepayment_elements}
                 WHERE pageid = :id";
        // Get the current max sequence on this page and add 1 to get the new sequence.
        if ($maxseq = $DB->get_record_sql($sql, ['id' => $pageid])) {
            $sequence = $maxseq->maxsequence + 1;
        }

        return $sequence;
    }

    /**
     * Return the list of possible elements to add.
     *
     * @return array the list of element types that can be used.
     * @throws \coding_exception
     */
    public static function get_available_element_types(): array {
        global $CFG;

        // Array to store the element types.
        $options = [];

        // Check that the directory exists.
        $elementdir = "$CFG->dirroot/enrol/coursepayment/classes/invoice/element";

        if (file_exists($elementdir)) {
            // Get directory contents.
            $elementfolders = new \DirectoryIterator($elementdir);
            // Loop through the elements folder.
            foreach ($elementfolders as $elementfolder) {
                // If it is not a directory or it is '.' or '..', skip it.
                if (!$elementfolder->isDir() || $elementfolder->isDot()) {
                    continue;
                }
                // Check that the standard class exists, if not we do
                // not want to display it as an option as it will not work.
                $foldername = $elementfolder->getFilename();

                // Get the class name.
                $classname = '\\enrol_coursepayment\\invoice\\element\\' . $foldername . '\\element';

                // Ensure the necessary class exists.
                if (class_exists($classname)) {
                    $options[$foldername] = get_string('invoice_element_' . $foldername, 'enrol_coursepayment');
                }
            }
        }

        \core_collator::asort($options);

        return $options;
    }

}
