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
 * The class representing the form to choose the item to create
 *
 * @package   mod_surveypro
 * @copyright 2022 onwards kordan <kordan@mclink.it>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_surveypro\local\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The class representing the form to choose the item to create
 *
 * @package   mod_surveypro
 * @copyright 2022 onwards kordan <kordan@mclink.it>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class itemchooser extends \moodleform {

    /**
     * Definition.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        $fieldname = 'typeplugin';
        $items = [];

        // Fields plugin.
        $plugins = \core_component::get_plugin_list('surveyprofield');
        $elements = [];
        foreach ($plugins as $plugin => $unused) {
            $elements['field_'.$plugin] = get_string('userfriendlypluginname', 'surveyprofield_'.$plugin);
        }
        asort($elements);
        $items[get_string('typefield', 'mod_surveypro')] = $elements;

        // Format plugin.
        $plugins = \core_component::get_plugin_list('surveyproformat');
        $elements = [];
        foreach ($plugins as $plugin => $unused) {
            $elements['format_'.$plugin] = get_string('userfriendlypluginname', 'surveyproformat_'.$plugin);
        }
        asort($elements);
        $items[get_string('typeformat', 'mod_surveypro')] = $elements;

        $elementgroup = [];
        $elementgroup[] = $mform->createElement('selectgroups', $fieldname, '', $items);
        $elementgroup[] = $mform->createElement('submit', $fieldname.'_button', get_string('add'));
        $mform->addGroup($elementgroup, $fieldname.'_group', get_string($fieldname, 'mod_surveypro'), [' '], false);
        $mform->addHelpButton($fieldname.'_group', $fieldname, 'surveypro');
    }
}
