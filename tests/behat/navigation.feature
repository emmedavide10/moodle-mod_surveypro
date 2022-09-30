@mod @mod_surveypro
Feature: verify urls really redirect to existing pages
  In order to verify urls really redirect to existing pages // Why this feature is useful
  As a teacher and as a student                             // It can be 'an admin', 'a teacher', 'a student', 'a guest', 'a user', 'a tests writer' and 'a developer'
  I select each link                                        // The feature we want

  Background:
    Given the following "courses" exist:
      | fullname          | shortname | category | groupmode |
      | Test links course | Tl course | 0        | 0         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | teacher  | teacher1@nowhere.net |
      | student1 | Student   | user1    | student1@nowhere.net |
    And the following "course enrolments" exist:
      | user     | course    | role           |
      | teacher1 | Tl course | editingteacher |
      | student1 | Tl course | student        |
    And the following "activities" exist:
      | activity  | name            | intro           | course    |
      | surveypro | sPro test links | To verify links | Tl course |
    And surveypro "sPro test links" contains the following items:
      | type  | plugin  |
      | format | label       |
      | format | fieldset    |
      | field  | age         |
      | field  | autofill    |
      | field  | boolean     |
      | field  | character   |
      | field  | checkbox    |
      | field  | date        |
      | field  | datetime    |
      | field  | fileupload  |
      | field  | integer     |
      | field  | multiselect |
      | field  | numeric     |
      | format | fieldsetend |
      | field  | radiobutton |
      | format | pagebreak   |
      | field  | rate        |
      | field  | recurrence  |
      | field  | select      |
      | field  | shortdate   |
      | field  | textarea    |
      | field  | time        |

  @javascript
  Scenario: test links as teacher
    Given I log in as "teacher1"
    And I am on "Test links course" course homepage
    And I follow "sPro test links"

    # Surveypro
    And I navigate to "Surveypro" in current page administration
    And I follow "Dashboard" page in tab bar
    And I follow "Responses" page in tab bar

    # Layout
    And I navigate to "Layout" in current page administration
    And I follow "Preview" page in tab bar
    And I follow "Elements" page in tab bar

    # Tools
    And I navigate to "Tools" in current page administration
    And I follow "Import" page in tab bar
    And I follow "Export" page in tab bar

    # Report
    And I navigate to "Report" in current page administration
    And I follow "Attachments overview" page in tab bar
    And I follow "Frequency distribution" page in tab bar
    And I follow "Late users" page in tab bar
    And I follow "Responses per user" page in tab bar
    And I follow "Users per count of responses" page in tab bar

    # User templates
    And I navigate to "User templates" in current page administration
    And I follow "Manage" page in tab bar
    And I follow "Save" page in tab bar
    And I follow "Import" page in tab bar
    And I follow "Apply" page in tab bar

    # Master templates
    And I navigate to "Master templates" in current page administration
    And I follow "Save" page in tab bar
    And I follow "Apply" page in tab bar

    # Surveypro > dashboard
    # Reports section
    And I navigate to "Surveypro" in current page administration
    And I follow "Run Attachments overview report"

    And I navigate to "Surveypro" in current page administration
    And I follow "Run Frequency distribution report"

    And I navigate to "Surveypro" in current page administration
    And I follow "Run Late users report"

    And I navigate to "Surveypro" in current page administration
    And I follow "Run Responses per user report"

    And I navigate to "Surveypro" in current page administration
    And I follow "Run Users per count of responses report"

    And I navigate to "Surveypro" in current page administration
    And I follow "Manage user templates"

    # User templates section
    And I navigate to "Surveypro" in current page administration
    And I follow "Manage"
    And I follow "Save"
    And I follow "Import"
    And I follow "Apply"

    # Master templates section
    And I navigate to "Surveypro" in current page administration
    And I follow "Save"
    And I follow "Apply"

  @javascript
  Scenario: test links as student
    Given I log in as "student1"
    And I am on "Test links course" course homepage
    And I follow "sPro test links"
    And I follow "Dashboard" page in tab bar
    And I follow "Responses" page in tab bar
