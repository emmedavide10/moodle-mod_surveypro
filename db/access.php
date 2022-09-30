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
 * Capability definitions for the surveypro module
 *
 * The capabilities are loaded into the database table when the module is
 * installed or updated. Whenever the capability definitions are updated,
 * the module version number should be bumped up.
 *
 * The system has four possible values for a capability:
 * CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT, and inherit (not set).
 *
 * It is important that capability names are unique. The naming convention
 * for capabilities that are specific to modules and blocks is as follows:
 *   [mod/block]/<plugin_name>:<capabilityname>
 *
 * component_name should be the same as the directory name of the mod or block.
 *
 * Core moodle capabilities are defined thus:
 *    moodle/<capabilityclass>:<capabilityname>
 *
 * Examples: mod/forum:viewpost
 *           block/recent_activity:view
 *           moodle/site:deleteuser
 *
 * The variable name for the capability definitions array is $capabilities
 *
 * @package   mod_surveypro
 * @copyright 2022 onwards kordan <kordan@mclink.it>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *  Let's start with a summary.
 *  This is the list of TABS detailed with corresponding sub-tabs and php file name.
 *  For each sub-tab, I would define a capability at first but, I will find, sometimes it is useless.
 *
 *  -------------------------------------------
 *  TWO MODULE GENERAL CAPABILITIES
 *  -------------------------------------------
 *  mod/surveypro:addinstance
 *  mod/surveypro:view
 *
 *  -------------------------------------------
 *  1) TAB TABDATAENTRY
 *  -------------------------------------------
 *  SUB-TAB == SURVEYPRO_DATAENTRY_COVER
 *
 *  SUB-TAB == SURVEYPRO_DATAENTRY_INSERT
 *      file: view_form.php
 *
 *      mod/surveypro:view
 *      mod/surveypro:accessreserveditems
 *      mod/surveypro:submit
 *      mod/surveypro:ignoremaxentries
 *
 *  SUB-TAB == SURVEYPRO_DATAENTRY_MANAGE
 *      file: view_submissions.php
 *
 *      mod/surveypro:alwaysseeowner
 *
 *      mod/surveypro:seeownsubmissions <-- It does not actually exist. It is always allowed.
 *      mod/surveypro:seeotherssubmissions
 *
 *      mod/surveypro:editownsubmissions
 *      mod/surveypro:editotherssubmissions
 *
 *      mod/surveypro:duplicateownsubmissions
 *      mod/surveypro:duplicateotherssubmissions
 *
 *      mod/surveypro:deleteownsubmissions
 *      mod/surveypro:deleteotherssubmissions
 *
 *      mod/surveypro:savesubmissiontopdf
 *
 *  SUB-TAB == SURVEYPRO_DATAENTRY_EDIT
 *      file: view_submissions.php
 *
 *  SUB-TAB == SURVEYPRO_DATAENTRY_READONLY
 *      file: view_form.php
 *
 *  SUB-TAB == SURVEYPRO_DATAENTRY_SEARCH
 *      file: view_search.php
 *
 *      mod/surveypro:searchsubmissions
 *
 *  -------------------------------------------
 *  2) TAB LAYOUT
 *  -------------------------------------------
 *  SUB-TAB == SURVEYPRO_LAYOUT_PREVIEW
 *      file: layout_preview.php
 *
 *      mod/surveypro:preview
 *
 *  SUB-TAB == SURVEYPRO_LAYOUT_ITEMSLIST
 *      file: layout_itemslist.php
 *
 *      mod/surveypro:manageitems
 *      mod/surveypro:additems
 *
 *  SUB-TAB == SURVEYPRO_LAYOUT_ITEMSLISTETUP
 *      file: layout_itemsetup.php
 *
 *  SUB-TAB == SURVEYPRO_LAYOUT_VALIDATE
 *      file: layout_validation.php
 *
 *  -------------------------------------------
 *  3) TAB TOOLS
 *  -------------------------------------------
 *  SUB-TAB == SURVEYPRO_TOOLS_IMPORT
 *      file: tools_import.php
 *
 *      mod/surveypro:importresponses
 *
 *  SUB-TAB == SURVEYPRO_TOOLS_EXPORT
 *      file: tools_export.php
 *
 *      mod/surveypro:exportresponses
 *
 *  -------------------------------------------
 *  4) TAB REPORTS
 *  -------------------------------------------
 *  SUB-TAB == SURVEYPRO_REPORT
 *      file: view_report.php
 *
 *      mod/surveypro:accessreports
 *
 *  -------------------------------------------
 *  5) TAB USER TEMPLATES
 *  -------------------------------------------
 *  SUB-TAB == SURVEYPRO_UTEMPLATES_MANAGE
 *      file: utemplate_manage.php
 *
 *      mod/surveypro:manageusertemplates
 *      mod/surveypro:deleteusertemplates
 *      mod/surveypro:downloadusertemplates
 *
 *  SUB-TAB == SURVEYPRO_UTEMPLATES_BUILD
 *      file: utemplate_save.php
 *
 *      mod/surveypro:saveusertemplates @ CONTEXT_COURSE
 *
 *  SUB-TAB == SURVEYPRO_UTEMPLATES_IMPORT
 *      file: utemplate_import.php
 *
 *      mod/surveypro:importusertemplates
 *
 *  SUB-TAB == SURVEYPRO_UTEMPLATES_APPLY
 *      file: utemplate_apply.php
 *
 *      mod/surveypro:applyusertemplates
 *
 *  -------------------------------------------
 *  6) TAB MASTER TEMPLATES
 *  -------------------------------------------
 *  SUB-TAB == SURVEYPRO_MTEMPLATES_BUILD
 *      file: mtemplate_save.php
 *
 *      mod/surveypro:savemastertemplates
 *
 *  SUB-TAB == SURVEYPRO_MTEMPLATES_APPLY
 *      file: mtemplate_apply.php
 *
 *      mod/surveypro:applymastertemplates
 *
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'mod/surveypro:addinstance' => [
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ],
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ],

    'mod/surveypro:view' => [

        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'guest' => CAP_ALLOW,
            'frontpage' => CAP_ALLOW,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ]
    ],

    'mod/surveypro:manageitems' => [
        'riskbitmask' => RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:additems' => [
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:accessreserveditems' => [

        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ],
        'clonepermissionsfrom' => 'mod/surveypro:accessadvanceditems'
    ],

    'mod/surveypro:preview' => [

        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ]
    ],

    'mod/surveypro:submit' => [
        'riskbitmask' => RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'frontpage' => CAP_ALLOW,
            'student' => CAP_ALLOW
        ]
    ],

    'mod/surveypro:ignoremaxentries' => [

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW
        ]
    ],

    'mod/surveypro:alwaysseeowner' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ]
    ],

    'mod/surveypro:seeotherssubmissions' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:editownsubmissions' => [
        'riskbitmask' => RISK_CONFIG | RISK_PERSONAL,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:editotherssubmissions' => [
        'riskbitmask' => RISK_CONFIG | RISK_PERSONAL,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:duplicateownsubmissions' => [
        'riskbitmask' => RISK_CONFIG | RISK_PERSONAL,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:duplicateotherssubmissions' => [
        'riskbitmask' => RISK_CONFIG | RISK_PERSONAL,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:deleteownsubmissions' => [
        'riskbitmask' => RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:deleteotherssubmissions' => [
        'riskbitmask' => RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:savesubmissiontopdf' => [

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ]
    ],

    'mod/surveypro:searchsubmissions' => [

        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ]
    ],

    'mod/surveypro:accessreports' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:accessownreports' => [

        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'student' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:importresponses' => [
        'riskbitmask' => RISK_CONFIG | RISK_PERSONAL,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:exportresponses' => [

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:manageusertemplates' => [

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:deleteusertemplates' => [
        'riskbitmask' => RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:downloadusertemplates' => [

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:saveusertemplates' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:importusertemplates' => [

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:applyusertemplates' => [
        'riskbitmask' => RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:savemastertemplates' => [

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],

    'mod/surveypro:applymastertemplates' => [
        'riskbitmask' => RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ]
];

$deprecatedcapabilities = [
    'mod/surveypro:importdata' => [
        'replacement' => 'mod/surveypro:importresponses',
        'message' => 'This capability was replaced with mod/surveypro:importresponses'
    ],

    'mod/surveypro:exportdata' => [
        'replacement' => 'mod/surveypro:exportresponses',
        'message' => 'This capability was replaced with mod/surveypro:exportresponses'
    ]
];
