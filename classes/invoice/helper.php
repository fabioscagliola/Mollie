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
 * Helper functions
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 */

namespace enrol_coursepayment\invoice;

/**
 * Class helper
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2018 MFreak.nl
 * @author    Luuk Verhoeven
 */
final class helper {

    /**
     * Handles uploading an image for the customcert module.
     *
     * @param int $draftitemid the draft area containing the files
     * @param int $contextid   the context we are storing this image in
     * @param string $filearea indentifies the file area.
     *
     * @return void
     */
    public static function upload_files(int $draftitemid, int $contextid, string $filearea = 'image'): void {
        global $CFG;

        // Save the file if it exists that is currently in the draft area.
        require_once($CFG->dirroot . '/lib/filelib.php');
        file_save_draft_area_files($draftitemid, $contextid, 'enrol_coursepayment', $filearea, 0);
    }

    /**
     * Return the list of possible fonts to use.
     *
     * @return array
     */
    public static function get_fonts(): array {
        global $CFG;

        require_once($CFG->libdir . '/pdflib.php');

        $arrfonts = [];
        $pdf = new \pdf();
        $fontfamilies = $pdf->get_font_families();
        foreach ($fontfamilies as $fontfamily => $fontstyles) {
            foreach ($fontstyles as $fontstyle) {
                $fontstyle = strtolower($fontstyle);
                if ($fontstyle == 'r') {
                    $filenamewoextension = $fontfamily;
                } else {
                    $filenamewoextension = $fontfamily . $fontstyle;
                }
                $fullpath = \TCPDF_FONTS::_getfontpath() . $filenamewoextension;
                // Set the name of the font to null, the include next should then set this
                // value, if it is not set then the file does not include the necessary data.
                $name = null;
                // Some files include a display name, the include next should then set this
                // value if it is present, if not then $name is used to create the display name.
                $displayname = null;
                // Some of the TCPDF files include files that are not present, so we have to
                // suppress warnings, this is the TCPDF libraries fault, grrr.
                @include($fullpath . '.php');
                // If no $name variable in file, skip it.
                if (is_null($name)) {
                    continue;
                }
                // Check if there is no display name to use.
                if (is_null($displayname)) {
                    // Format the font name, so "FontName-Style" becomes "Font Name - Style".
                    $displayname = preg_replace("/([a-z])([A-Z])/", "$1 $2", $name);
                    $displayname = preg_replace("/([a-zA-Z])-([a-zA-Z])/", "$1 - $2", $displayname);
                }

                $arrfonts[$filenamewoextension] = $displayname;
            }
        }
        ksort($arrfonts);

        return $arrfonts;
    }

    /**
     * Return the list of possible font sizes to use.
     *
     * @return array
     */
    public static function get_font_sizes(): array {
        // Array to store the sizes.
        $sizes = [];

        for ($i = 1; $i <= 200; $i++) {
            $sizes[$i] = $i;
        }

        return $sizes;
    }

}
