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
 * Class to filter output by group
 *
 * @package   surveyproreport_colles
 * @copyright 2022 onwards kordan <kordan@mclink.it>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace surveyproreport_colles;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The class to filter the attachment item to overview
 *
 * @package   surveyproreport_colles
 * @copyright 2022 onwards kordan <kordan@mclink.it>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class groupjumperform extends \moodleform {

    /**
     * Definition.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        // Get _customdata.
        $showjumper = $this->_customdata->showjumper;
        $canaccessallgroups = $this->_customdata->canaccessallgroups;
        $addnotinanygroup = $this->_customdata->addnotinanygroup;
        $jumpercontent = $this->_customdata->jumpercontent;

        $elementgroup = [];

        $fieldname = 'type';
        $options = ['summary' => 'summary', 'scales' => 'scales', 'questions' => 'questions'];
        $elementgroup[] =& $mform->createElement('select', $fieldname, '', $options);
        // $mform->SetDefault($fieldname, $type);
        // $mform->addElement('select', $fieldname, get_string('type', 'mod_surveypro'), $options);

        $fieldname = 'area';
        $options = [];
        $options[] = get_string('fieldset_content_01', 'surveyprotemplate_collesactual');
        $options[] = get_string('fieldset_content_02', 'surveyprotemplate_collesactual');
        $options[] = get_string('fieldset_content_03', 'surveyprotemplate_collesactual');
        $options[] = get_string('fieldset_content_04', 'surveyprotemplate_collesactual');
        $options[] = get_string('fieldset_content_05', 'surveyprotemplate_collesactual');
        $options[] = get_string('fieldset_content_06', 'surveyprotemplate_collesactual');
        $elementgroup[] =& $mform->createElement('select', $fieldname, '', $options);
        // $mform->SetDefault($fieldname, $area);

        $mform->addGroup($elementgroup, 'type_area', get_string('type', 'mod_surveypro'), [' '], true);
        $mform->disabledIf('type_area[area]', 'type_area[type]', 'neq', 'questions');
        // $mform->addElement('select', $fieldname, get_string('area', 'mod_surveypro'), $options);

        if ($showjumper) {
            $fieldname = 'groupid';
            $options = [];
            if ($canaccessallgroups) {
                $options[] = get_string('allgroups');
            }
            if ($addnotinanygroup) {
                $options['-1'] = get_string('notinanygroup', 'surveyproreport_attachments');
            }
            foreach ($jumpercontent as $group) {
                $options[$group->id] = $group->name;
            }

            $mform->addElement('select', $fieldname, get_string('group', 'group'), $options, $options);
        }

        $mform->addElement('submit', 'submitbutton', get_string('reload'));
    }
}

