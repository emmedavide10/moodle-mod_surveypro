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
 * Surveypro tabs class.
 *
 * @package   mod_surveypro
 * @copyright 2022 onwards kordan <kordan@mclink.it>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_surveypro;

defined('MOODLE_INTERNAL') || die();

use mod_surveypro\utility_layout;

require_once($CFG->libdir.'/adminlib.php');

/**
 * The class representing the tab-page structure on top of every page of the module
 *
 * @package   mod_surveypro
 * @copyright 2022 onwards kordan <kordan@mclink.it>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tabs {

    /**
     * @var object Course module object
     */
    protected $cm;

    /**
     * @var object Context object
     */
    protected $context;

    /**
     * @var object Surveypro object
     */
    protected $surveypro;

    /**
     * Class constructor.
     *
     * @param object $cm
     * @param object $context
     * @param object $surveypro
     */
    public function __construct($cm, $context, $surveypro) {
        $this->cm = $cm;
        $this->context = $context;
        $this->surveypro = $surveypro;
    }

    /**
     * Print the secundary navigation level
     *
     * @param int $parenttab
     * @param string $activepage
     */
    public function draw_pages_bar($parenttab, $activepage) {
        $tabs = [];
        $inactive = [];

        // Pages definition.
        $tabs[] = $this->get_pages_list($parenttab, $activepage);
        $activated = [$activepage];
        \print_tabs($tabs, '', $inactive, $activated);
    }

    /**
     * Get pages structure
     *
     * @param int $parenttab
     * @param string $activepage
     */
    private function get_pages_list($parenttab, $activepage) {
        global $DB;

        $canmanageitems = has_capability('mod/surveypro:manageitems', $this->context);
        $canaccessreserveditems = has_capability('mod/surveypro:accessreserveditems', $this->context);

        $utilitylayoutman = new utility_layout($this->cm, $this->surveypro);
        $itemcount = $utilitylayoutman->has_items(0, 'field', $canmanageitems, $canaccessreserveditems, true);

        $tabs = [];

        $paramurlbase = ['id' => $this->cm->id];
        switch ($parenttab) {
            case SURVEYPRO_TABDATAENTRY:
                // Cover.
                $elementurl = new \moodle_url('/mod/surveypro/view.php', $paramurlbase);
                $strlabel = get_string('tabdataentrypage1', 'mod_surveypro');
                $tabs[] = new \tabobject('cover_page', $elementurl->out(), $strlabel);

                // Responses.
                $elementurl = new \moodle_url('/mod/surveypro/view_submissions.php', $paramurlbase);
                $strlabel = get_string('tabdataentrypage2', 'mod_surveypro');
                if ($itemcount) {
                    $tabs[] = new \tabobject('submissionslist_page', $elementurl->out(), $strlabel);
                } else {
                    $tabs[] = new \tabobject('submissionslist_page', null, $strlabel);
                }

                // Insert.
                if ($activepage == 'newresponse_page') {
                    $strlabel = get_string('tabdataentrypage3', 'mod_surveypro');
                    $tabs[] = new \tabobject('newresponse_page', null, $strlabel);
                }

                // Edit.
                if ($activepage == 'editresponse_page') {
                    $strlabel = get_string('tabdataentrypage4', 'mod_surveypro');
                    $tabs[] = new \tabobject('editresponse_page', null, $strlabel);
                }

                // Read only.
                if ($activepage == 'roresponse_page') {
                    $strlabel = get_string('tabdataentrypage5', 'mod_surveypro');
                    $tabs[] = new \tabobject('roresponse_page', null, $strlabel);
                }

                // Search.
                $utilitylayoutman = new utility_layout($this->cm, $this->surveypro);
                $condition = has_capability('mod/surveypro:searchsubmissions', $this->context);
                $condition = $condition && $utilitylayoutman->has_search_items();
                if ($condition) {
                    $elementurl = new \moodle_url('/mod/surveypro/view_search.php', $paramurlbase);
                    $strlabel = get_string('tabdataentrypage6', 'mod_surveypro');
                    $tabs[] = new \tabobject('search_page', $elementurl->out(), $strlabel);
                }

                break;

            case SURVEYPRO_TABLAYOUT:
                $canpreview = has_capability('mod/surveypro:preview', $this->context);

                $where = 'surveyproid = :surveyproid AND parentid <> :parentid';
                $whereparams = ['surveyproid' => $this->surveypro->id, 'parentid' => 0];
                $countparents = $DB->count_records_select('surveypro_item', $where, $whereparams);

                // Preview.
                if ($canpreview) {
                    $elementurl = new \moodle_url('/mod/surveypro/layout_preview.php', $paramurlbase);
                    $strlabel = get_string('tablayoutpage1', 'mod_surveypro');
                    $tabs[] = new \tabobject('preview_page', $elementurl->out(), $strlabel);
                }

                // Item management.
                if ($canmanageitems) {
                    $elementurl = new \moodle_url('/mod/surveypro/layout_itemslist.php', $paramurlbase);
                    $strlabel = get_string('tablayoutpage2', 'mod_surveypro');
                    $tabs[] = new \tabobject('itemslist_page', $elementurl->out(), $strlabel);
                }

                // Element setup.
                $condition = $canmanageitems;
                $condition = $condition && empty($this->surveypro->template);
                $condition = $condition && ($activepage == 'itemsetup_page');
                if ($condition) {
                    $elementurl = new \moodle_url('/mod/surveypro/layout_itemsetup.php', $paramurlbase);
                    $strlabel = get_string('tablayoutpage3', 'mod_surveypro');
                    $tabs[] = new \tabobject('itemsetup_page', null, $strlabel);
                }

                // Relation validation.
                $condition = $canmanageitems;
                $condition = $condition && empty($this->surveypro->template);
                $condition = $condition && $countparents;
                if ($condition) {
                    $elementurl = new \moodle_url('/mod/surveypro/layout_validation.php', $paramurlbase);
                    $strlabel = get_string('tablayoutpage4', 'mod_surveypro');
                    $tabs[] = new \tabobject('validation_page', $elementurl->out(), $strlabel);
                }

                break;

            case SURVEYPRO_TABTOOLS:
                // Import.
                if (has_capability('mod/surveypro:importresponses', $this->context)) {
                    $elementurl = new \moodle_url('/mod/surveypro/tools_import.php', $paramurlbase);
                    $strlabel = get_string('tabtoolspage1', 'mod_surveypro');
                    $tabs[] = new \tabobject('import_page', $elementurl->out(), $strlabel);
                }

                // Export.
                if (has_capability('mod/surveypro:exportresponses', $this->context)) {
                    $elementurl = new \moodle_url('/mod/surveypro/tools_export.php', $paramurlbase);
                    $strlabel = get_string('tabtoolspage2', 'mod_surveypro');
                    $tabs[] = new \tabobject('export_page', $elementurl->out(), $strlabel);
                }

                break;

            case SURVEYPRO_TABREPORTS:
                $surveyproreportlist = \core_component::get_plugin_list('surveyproreport');

                foreach ($surveyproreportlist as $reportname => $reportpath) {
                    $classname = 'surveyproreport_'.$reportname.'\report';
                    $reportman = new $classname($this->cm, $this->context, $this->surveypro);

                    if ($reportman->is_report_allowed($reportname)) {
                        $elementurl = new \moodle_url('/mod/surveypro/report/'.$reportname.'/view.php', $paramurlbase);
                        $strlabel = get_string('pluginname', 'surveyproreport_'.$reportname);
                        $tabs[] = new \tabobject($reportname, $elementurl->out(), $strlabel);
                    }
                }

                break;

            case SURVEYPRO_TABUTEMPLATES:
                // Manage.
                if (has_capability('mod/surveypro:manageusertemplates', $this->context)) {
                    $elementurl = new \moodle_url('/mod/surveypro/utemplate_manage.php', $paramurlbase);
                    $strlabel = get_string('tabutemplatepage1', 'mod_surveypro');
                    $tabs[] = new \tabobject('utemplatemanage_page', $elementurl->out(), $strlabel);
                }

                // Save.
                if (has_capability('mod/surveypro:saveusertemplates', $this->context)) {
                    $elementurl = new \moodle_url('/mod/surveypro/utemplate_save.php', $paramurlbase);
                    $strlabel = get_string('tabutemplatepage2', 'mod_surveypro');
                    $tabs[] = new \tabobject('utemplatesave_page', $elementurl->out(), $strlabel);
                }

                // Import.
                if (has_capability('mod/surveypro:importusertemplates', $this->context)) {
                    $elementurl = new \moodle_url('/mod/surveypro/utemplate_import.php', $paramurlbase);
                    $strlabel = get_string('tabutemplatepage3', 'mod_surveypro');
                    $tabs[] = new \tabobject('utemplateimport_page', $elementurl->out(), $strlabel);
                }

                // Apply.
                if (has_capability('mod/surveypro:applyusertemplates', $this->context)) {
                    $elementurl = new \moodle_url('/mod/surveypro/utemplate_apply.php', $paramurlbase);
                    $strlabel = get_string('tabutemplatepage4', 'mod_surveypro');
                    $tabs[] = new \tabobject('utemplateapply_page', $elementurl->out(), $strlabel);
                }

                break;

            case SURVEYPRO_TABMTEMPLATES:
                // Create.
                if (has_capability('mod/surveypro:savemastertemplates', $this->context)) {
                    $elementurl = new \moodle_url('/mod/surveypro/mtemplate_save.php', $paramurlbase);
                    $strlabel = get_string('tabmtemplatepage1', 'mod_surveypro');
                    $tabs[] = new \tabobject('mtemplatecreate_page', $elementurl->out(), $strlabel);
                }

                // Apply.
                if (has_capability('mod/surveypro:applymastertemplates', $this->context)) {
                    $elementurl = new \moodle_url('/mod/surveypro/mtemplate_apply.php', $paramurlbase);
                    $strlabel = get_string('tabmtemplatepage2', 'mod_surveypro');
                    $tabs[] = new \tabobject('mtemplateapply_page', $elementurl->out(), $strlabel);
                }

                break;

            default:
                throw new \moodle_exception('incorrectaccessdetected', 'mod_surveypro');
        }

        return $tabs;
    }
}
