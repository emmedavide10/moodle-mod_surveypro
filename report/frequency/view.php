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
 * Starting page of the frequency report.
 *
 * @package   surveyproreport_frequency
 * @copyright 2013 onwards kordan <stringapiccola@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_surveypro\utility_layout;
use mod_surveypro\tabs;
use surveyproreport_frequency\filterform;
use surveyproreport_frequency\report;

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
require_once($CFG->dirroot.'/mod/surveypro/report/frequency/lib.php');
require_once($CFG->libdir.'/tablelib.php');

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

$edit = optional_param('edit', -1, PARAM_BOOL);

require_course_login($course, false, $cm);
$context = \context_module::instance($cm->id);

// Required capability.
require_capability('mod/surveypro:accessreports', $context);

$utilitylayoutman = new utility_layout($cm, $surveypro);
$reportman = new report($cm, $context, $surveypro);

// Begin of: instance filterform.
$showjumper = $reportman->is_groupjumper_needed();

$paramurl = ['id' => $cm->id];
$formurl = new \moodle_url('/mod/surveypro/report/frequency/view.php', $paramurl);

$formparams = new \stdClass();
$formparams->surveypro = $surveypro;
$formparams->showjumper = $showjumper;
if ($showjumper) {
    $canaccessallgroups = has_capability('moodle/site:accessallgroups', $context);
    $jumpercontent = $reportman->get_groupjumper_items();

    $formparams->canaccessallgroups = $canaccessallgroups;
    $formparams->addnotinanygroup = $reportman->add_notinanygroup();
    $formparams->jumpercontent = $jumpercontent;
}
$filterform = new filterform($formurl, $formparams); // No autosubmit, here.
// End of: instance filterform.

// Output starts here.
$url = new \moodle_url('/mod/surveypro/report/frequency/view.php', ['s' => $surveypro->id]);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_cm($cm);
$PAGE->set_title($surveypro->name);
$PAGE->set_heading($course->shortname);

echo $OUTPUT->header();
// echo $OUTPUT->heading(format_string($surveypro->name), 2, null);

$surveyproreportlist = get_plugin_list('surveyproreport');
$reportkey = array_search('frequency', array_keys($surveyproreportlist));
new tabs($cm, $context, $surveypro, SURVEYPRO_TABREPORTS, $reportkey);

$reportman->prevent_direct_user_input();
$reportman->stop_if_textareas_only();

// Begin of: manage form submission.
if ($fromform = $filterform->get_data()) {
    $itemid = $fromform->itemid;
    if ($showjumper) {
        $groupid = $fromform->groupid;
        $reportman->set_groupid($groupid);
    }
}
// End of: manage form submission.

$filterform->display();

if (!empty($itemid)) {
    $reportman->setup_outputtable($itemid);
    $reportman->fetch_data($itemid);

    $paramurl = array();
    $paramurl['id'] = $cm->id;
    if ($showjumper) {
        $paramurl['groupid'] = $groupid;
    }
    $paramurl['itemid'] = $itemid;
    $url = new \moodle_url('/mod/surveypro/report/frequency/graph.php', $paramurl);
    // To troubleshoot graph, open a new window in the broser and directly call
    // http://localhost/head/mod/surveypro/report/frequency/graph.php?id=xx&groupid=0&itemid=yyy
    // address.

    $reportman->output_data($url);
}

// Finish the page.
echo $OUTPUT->footer();
