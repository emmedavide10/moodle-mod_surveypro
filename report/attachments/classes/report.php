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
 * Surveypro class to manage attachment overview report
 *
 * @package   surveyproreport_attachments
 * @copyright 2022 onwards kordan <kordan@mclink.it>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace surveyproreport_attachments;

defined('MOODLE_INTERNAL') || die();

use mod_surveypro\reportbase;

require_once($CFG->libdir.'/tablelib.php');

/**
 * The class to manage attachment overview report.
 *
 * @package   surveyproreport_attachments
 * @copyright 2022 onwards kordan <kordan@mclink.it>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report extends reportbase {

    /**
     * @var flexible_table $outputtable
     */
    public $outputtable = null;

    /**
     * @var int ID of the user related to report details
     */
    public $userid;

    /**
     * @var int ID of the item related to report details
     */
    public $itemid;

    /**
     * @var int ID of the saved suryey_submission
     */
    public $submissionid;

    /**
     * Class constructor.
     *
     * @param object $cm
     * @param object $context
     * @param object $surveypro
     */
    public function __construct($cm, $context, $surveypro) {
        parent::__construct($cm, $context, $surveypro);
    }

    /**
     * Returns if this report was created for student too.
     *
     * @return boolean false
     */
    public function has_studentreport() {
        return false;
    }

    /**
     * Does the current report apply to the passed mastertemplates?
     *
     * @param string $mastertemplate
     * @return void
     */
    public function report_applies_to($mastertemplate) {
        return true;
    }

    /**
     * Get if this report displays user names.
     *
     * @return boolean false
     */
    public function has_visibleusernames() {
        return true;
    }

    /**
     * Setup_outputtable.
     */
    public function setup_outputtable() {
        $this->outputtable = new \flexible_table('attachmentslist');

        $paramurl = ['id' => $this->cm->id];
        if ($this->groupid) {
            $paramurl['groupid'] = $this->groupid;
        }
        $baseurl = new \moodle_url('/mod/surveypro/report/attachments/view.php', $paramurl);
        $this->outputtable->define_baseurl($baseurl);

        $tablecolumns = [];
        $tablecolumns[] = 'picture';
        $tablecolumns[] = 'fullname';
        $tablecolumns[] = 'uploads';
        $this->outputtable->define_columns($tablecolumns);

        $tableheaders = [];
        $tableheaders[] = '';
        $tableheaders[] = get_string('fullname');
        $tableheaders[] = get_string('uploads', 'surveyproreport_attachments');
        $this->outputtable->define_headers($tableheaders);

        $this->outputtable->sortable(true);
        $this->outputtable->no_sorting('uploads');

        $this->outputtable->column_class('picture', 'picture');
        $this->outputtable->column_class('fullname', 'fullname');
        $this->outputtable->column_class('uploads', 'uploads');

        $this->outputtable->initialbars(true);

        // Hide the same info whether in two consecutive rows.
        $this->outputtable->column_suppress('picture');
        $this->outputtable->column_suppress('fullname');

        // General properties for the whole table.
        $this->outputtable->summary = get_string('submissionslist', 'mod_surveypro');
        $this->outputtable->set_attribute('cellpadding', '5');
        $this->outputtable->set_attribute('id', 'attachments');
        $this->outputtable->set_attribute('class', 'generaltable');
        $this->outputtable->setup();
    }

    /**
     * Fetch_data.
     *
     * This is the idea supporting the code.
     *
     * Teachers is the role of users usually accessing reports.
     * They are "teachers" so they care about "students" and nothing more.
     * If, at import time, some records go under the admin ownership
     * the teacher is not supposed to see them because admin is not a student.
     * In this case, if the teacher wants to see submissions owned by admin, HE HAS TO ENROLL ADMIN with some role.
     *
     * Different is the story for the admin.
     * If an admin wants to make a report, he will see EACH RESPONSE SUBMITTED
     * without care to the role of the owner of the submission.
     *
     * @return void
     */
    public function fetch_data() {
        global $DB, $COURSE, $OUTPUT;

        $displayuploadsstr = get_string('display_uploads', 'surveyproreport_attachments');
        $missinguploadsstr = get_string('missing_uploads', 'surveyproreport_attachments');
        $submissionidstr = get_string('submissionid', 'surveyproreport_attachments');

        list($sql, $whereparams) = $this->get_submissions_sql();
        $usersubmissions = $DB->get_recordset_sql($sql, $whereparams);

        foreach ($usersubmissions as $usersubmission) {
            $tablerow = [];

            // Picture.
            $tablerow[] = $OUTPUT->user_picture($usersubmission, ['courseid' => $COURSE->id]);

            // User fullname.
            $userfullname = fullname($usersubmission);
            $paramurl = ['id' => $usersubmission->id];
            $url = new \moodle_url('/user/view.php', $paramurl);
            $cellcontent = \html_writer::start_tag('a', ['title' => $userfullname, 'href' => $url]);
            $cellcontent .= $userfullname;
            $cellcontent .= \html_writer::end_tag('a');
            $cellcontent .= ' [id: '.$usersubmission->id.']';
            $tablerow[] = $cellcontent;
            // $tablerow[] = '<a href="'.$url->out().'">'.fullname($usersubmission).' [id: '.$usersubmission->id.']</a>';

            // Users with $usersubmission->submissionid == null have no submissions.
            if (!empty($usersubmission->submissionid)) {
                $paramurl = [];
                $paramurl['s'] = $this->surveypro->id;
                $paramurl['container'] = $usersubmission->id.'_'.$usersubmission->submissionid;
                $url = new \moodle_url('/mod/surveypro/report/attachments/uploads.php', $paramurl);
                $cellcontent = '('.$submissionidstr.': '.$usersubmission->submissionid.')&nbsp;';
                $cellcontent .= \html_writer::start_tag('a', ['title' => $displayuploadsstr, 'href' => $url]);
                $cellcontent .= s($displayuploadsstr);
                $cellcontent .= \html_writer::end_tag('a');
                $tablerow[] = $cellcontent;
            } else {
                $tablerow[] = $missinguploadsstr;
            }

            // Add row to the table.
            $this->outputtable->add_data($tablerow);
        }

        $usersubmissions->close();
    }

    /**
     * Get_submissions_sql
     *
     * @return [$sql, $whereparams];
     */
    public function get_submissions_sql() {
        global $COURSE, $DB;

        $userfieldsapi = \core_user\fields::for_userpic()->get_sql('u');
        $whereparams = [];
        $sql = 'SELECT s.id as submissionid'.$userfieldsapi->selects.'
                FROM {user} u
                JOIN {surveypro_submission} s ON s.userid = u.id';

        list($middlesql, $whereparams) = $this->get_middle_sql();
        $sql .= $middlesql;

        if ($this->outputtable->get_sql_sort()) {
            $sql .= ' ORDER BY '.$this->outputtable->get_sql_sort().', submissionid ASC';
        } else {
            $sql .= ' ORDER BY u.lastname ASC, submissionid ASC';
        }

        return [$sql, $whereparams];
    }

    /**
     * Output_data.
     *
     * @return void
     */
    public function output_data() {
        $this->outputtable->print_html();
    }

    /**
     * Check_attachmentitems.
     *
     * @return void
     */
    public function check_attachmentitems() {
        global $OUTPUT, $DB;

        $params = [];
        $params['surveyproid'] = $this->surveypro->id;
        $params['plugin'] = 'fileupload';
        $attachmentitems = $DB->count_records('surveypro_item', $params);

        if (!$attachmentitems) {
            $message = get_string('noattachmentitemsfound', 'surveyproreport_attachments');
            echo $OUTPUT->box($message, 'notice centerpara');
            echo $OUTPUT->footer();
            die();
        }
    }

    /**
     * Set user id.
     *
     * @param string $userid
     * @return void
     */
    public function set_userid($userid) {
        $this->userid = $userid;
    }

    /**
     * Set item id.
     *
     * @param string $itemid
     * @return void
     */
    public function set_itemid($itemid) {
        $this->itemid = $itemid;
    }

    /**
     * Set submission id.
     *
     * @param string $submissionid
     * @return void
     */
    public function set_submissionid($submissionid) {
        $this->submissionid = $submissionid;
    }

    /**
     * Display details of attachments for a given user and submission.
     *
     * @param int $submissionid
     * @param int $itemid
     * @return void
     */
    public function display_attachments($submissionid, $itemid) {
        global $CFG, $DB, $OUTPUT;

        $nofilesfound = get_string('nofilesfound', 'surveyproreport_attachments');

        $submission = $DB->get_record('surveypro_submission', ['id' => $submissionid], '*', MUST_EXIST);
        $user = $DB->get_record('user', ['id' => $submission->userid], '*', MUST_EXIST);

        $layout = <<<EOS
<div class="mform">
    <!-- <fieldset class="hidden"> -->
        <div>
            <div class="fitem">
                <div class="fitemtitle">
                    <div class="fstaticlabel">
                        <label>
                            <strong>@@left@@</strong>
                        </label>
                    </div>
                </div>
                <div class="mod-indent info-indent-1">
                </div>
                <div class="felement fstatic info-indent">
                    @@right@@
                </div>
            </div>
        </div>
    <!-- </fieldset> -->
</div>
EOS;

        $left = get_string('fullnameuser');
        $right = fullname($user);
        if (isset($CFG->forcefirstname) || isset($CFG->forcelastname)) {
            $right .= ' - id: '.$user->id;
        }
        $output = str_replace('@@left@@', $left, $layout);
        $output = str_replace('@@right@@', $right, $output);

        $left = get_string('submissioninfo', 'surveyproreport_attachments');
        $right = get_string('submissionid', 'surveyproreport_attachments').': '.$submission->id.'<br />';
        $right .= get_string('timecreated', 'mod_surveypro').': '.userdate($submission->timecreated).'<br />';
        if ($submission->timemodified) {
            $right .= get_string('timemodified', 'mod_surveypro').': '.userdate($submission->timemodified);
        } else {
            $right .= get_string('timemodified', 'mod_surveypro').': '.get_string('never');
        }

        $output .= str_replace('@@left@@', $left, $layout);
        $output = str_replace('@@right@@', $right, $output);

        $whereparams = ['submissionid' => $submissionid, 'plugin' => 'fileupload'];
        $sql = 'SELECT i.id, a.id as answerid, fu.content
                FROM {surveypro_item} i
                  JOIN {surveypro_answer} a ON a.itemid = i.id
                  JOIN {surveyprofield_fileupload} fu ON fu.itemid = a.itemid
                WHERE i.plugin = :plugin
                  AND a.submissionid = :submissionid';
        if ($itemid) {
            $sql .= ' AND i.id = :itemid ';
            $whereparams['itemid'] = $itemid;
        }
        $sql .= ' ORDER BY i.sortindex';

        $items = $DB->get_records_sql($sql, $whereparams);

        $fs = get_file_storage();
        $component = 'surveyprofield_fileupload';
        $filearea = 'fileuploadfiles';
        foreach ($items as $item) {
            if ($files = $fs->get_area_files($this->context->id, $component, $filearea, $item->answerid, 'timemodified', false)) {
                foreach ($files as $file) {
                    $filename = $file->get_filename();
                    $iconimage = $OUTPUT->pix_icon(file_file_icon($file, 80), get_mimetype_description($file));

                    $path = '/'.$this->context->id.'/surveyprofield_fileupload/'.$filearea.'/'.$item->answerid.'/'.$filename;
                    $url = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path);

                    $left = $item->content;
                    $right = '<a href="'.$url.'">'.$iconimage.'</a>';
                    $right .= '<a href="'.$url.'">'.s($filename).'</a>';
                    $output .= str_replace('@@left@@', $left, $layout);
                    $output = str_replace('@@right@@', $right, $output);
                }
            } else {
                $left = $item->content;
                $right = $nofilesfound;
                $output .= str_replace('@@left@@', $left, $layout);
                $output = str_replace('@@right@@', $right, $output);
            }
        }

        echo $output;
    }
}
