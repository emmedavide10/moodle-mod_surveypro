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
 * Starting page of the colles report.
 *
 * @package   surveyproreport_colles
 * @copyright 2022 onwards kordan <kordan@mclink.it>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_surveypro\tabs;
use surveyproreport_colles\report;
use surveyproreport_colles\groupjumperform;

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
require_once($CFG->dirroot.'/mod/surveypro/report/colles/lib.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/adminlib.php');

$id = optional_param('id', 0, PARAM_INT);
$s = optional_param('s', 0, PARAM_INT);

if (!empty($id)) {
    $cm = get_coursemodule_from_id('surveypro', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $surveypro = $DB->get_record('surveypro', ['id' => $cm->instance], '*', MUST_EXIST);
} else {
    $surveypro = $DB->get_record('surveypro', ['id' => $s], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $surveypro->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('surveypro', $surveypro->id, $course->id, false, MUST_EXIST);
}
$cm = cm_info::create($cm);

$type = optional_param('type', 'summary', PARAM_ALPHA);  // Type of graph.
$area = optional_param('area', 0, PARAM_INT);            // Area for 'question' graph.
$groupid = optional_param('groupid', false, PARAM_INT);  // Group ID.

require_course_login($course, false, $cm);
$context = \context_module::instance($cm->id);

if ($type == 'summary') {
    if (!has_capability('mod/surveypro:accessreports', $context)) {
        require_capability('mod/surveypro:accessownreports', $context);
    }
} else {
    require_capability('mod/surveypro:accessreports', $context);
}

$reportman = new report($cm, $context, $surveypro);
$reportman->set_area($area);
$reportman->set_groupid($groupid);

// Begin of: define $mform return url.
$showjumper = $reportman->is_groupjumper_needed();
$canaccessallgroups = has_capability('moodle/site:accessallgroups', $context);
$jumpercontent = $reportman->get_groupjumper_items();

$paramurl = ['id' => $cm->id];
$formurl = new \moodle_url('/mod/surveypro/report/colles/view.php', $paramurl);

$formparams = new \stdClass();
$formparams->showjumper = $showjumper;
$formparams->canaccessallgroups = $canaccessallgroups;
$formparams->addnotinanygroup = $reportman->add_notinanygroup();
$formparams->jumpercontent = $jumpercontent;
$groupfilterform = new groupjumperform($formurl, $formparams);
// End of: prepare params for the form.

// Output starts here.
$paramurl = ['s' => $surveypro->id, 'type' => $type];
if ( ($type == 'questions') && ($area !== false) ) { // Area can be zero.
    $paramurl['area'] = $area;
}

$url = new \moodle_url('/mod/surveypro/report/colles/view.php', $paramurl);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_cm($cm);
$PAGE->set_title($surveypro->name);
$PAGE->set_heading($course->shortname);

echo $OUTPUT->header();
$tab = new tabs($cm, $context, $surveypro);
$tab->draw_pages_bar(SURVEYPRO_TABREPORTS, 'colles');

$reportman->prevent_direct_user_input();

// Begin of: manage form submission.
if ($fromform = $groupfilterform->get_data()) {
    $type = isset($fromform->type_area['type']) ? $fromform->type_area['type'] : 'summary';
    $area = isset($fromform->type_area['area']) ? $fromform->type_area['area'] : 1;
    if ($showjumper) {
        $reportman->set_groupid($fromform->groupid);
    }
}
// End of: manage form submission.

$preset = ['type_area[type]' => $type, 'type_area[area]' => $area];
$groupfilterform->set_data($preset);
$groupfilterform->display();

$reportman->nosubmissions_stop();

switch ($type) {
    case 'summary':
        $reportman->output_summarydata();
        break;
    case 'scales':
        $reportman->output_scalesdata();
        break;
    case 'questions':
        $reportman->output_questionsdata($area);
        break;
}

// Finish the page.
echo $OUTPUT->footer();
