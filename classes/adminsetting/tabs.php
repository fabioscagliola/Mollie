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
 * Tabs setting
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2017 MFreak.nl
 * @author    Luuk Verhoeven
 */

namespace enrol_coursepayment\adminsetting;

/**
 * Class tabs
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2017 MFreak.nl
 * @author    Luuk Verhoeven
 */
class tabs extends \admin_setting {

    /**
     * @var array[] $tabs The tabs to display.
     */
    protected array $tabs = [0 => []];

    /**
     * @var mixed $selected The currently selected tab.
     */
    protected mixed $selected;

    /**
     * @var string $section The section name.
     */
    protected string $section;

    /**
     * Config fileupload constructor
     *
     * @param string $name     Unique ascii name, either 'mysetting' for settings that in
     *                         config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $section  Section name
     * @param string $default
     *
     * @throws \coding_exception
     */
    public function __construct(string $name, string $section, string $default = '') {
        parent::__construct($name, '', '', '');
        $this->section = $section;

        // Check for direct links.
        $this->selected = optional_param($this->get_full_name(), $default, PARAM_RAW);
    }

    /**
     * Return the currently selected tab.
     *
     * @return mixed The id of the currently selected tab.
     */
    public function get_setting(): mixed {
        return $this->selected;
    }

    /**
     * Write settings.
     *
     * In practice this actually runs the reset, import or export sub actions.
     *
     * @param array $params The submitted data to act upon.
     *
     * @return string Always returns an empty string
     */
    public function write_setting($params): string {
        $result = '';

        if (isset($params['action'])) {

            if ($params['action'] == 1) {
                $result = $this->reset();

            } else if ($params['action'] == 2) {
                $result = $this->import($params['picker']);

            } else if ($params['action'] == 3) {
                $result = $this->export();
            }
        }

        return $result;
    }

    /**
     * Add a tab to the tab row
     *
     * For now, we only implement a single row.  Multiple rows could be added as an extension
     * later.
     *
     * @param string $id      The tab id
     * @param string $name The tab name
     *
     * @throws \moodle_exception
     */
    public function addtab(string $id, string $name): void {

        $urlparams = [
            'section' => $this->section,
            $this->get_full_name() => $id,
        ];
        $url = new \moodle_url('/admin/settings.php', $urlparams);
        $tab = new \tabobject($id, $url, $name);

        $this->tabs[0][] = $tab;
    }

    /**
     * Returns an HTML string
     *
     * @param mixed $params Array or string depending on setting
     * @param string $query Query
     *
     * @return string Returns an HTML string
     */
    public function output_html($params, $query = ''): string {

        $html = print_tabs($this->tabs, $this->selected, null, null, true);

        $properties = [
            'type' => 'hidden',
            'name' => $this->get_full_name(),
            'value' => $this->get_setting(),
        ];

        $html .= \html_writer::empty_tag('input', $properties);

        $properties['id'] = $this->get_id();
        $properties['name'] = $this->get_full_name() . '_new';

        $html .= \html_writer::empty_tag('input', $properties);

        return $html;
    }

}
