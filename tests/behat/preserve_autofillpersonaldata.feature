@mod @mod_surveypro @current
Feature: editing a submission, autofill userID is not overwritten
  In order to test that personal data is not overwritten editing a submission
  As student1 and student2
  I fill a surveypro and edit it as different user

  @javascript
  Scenario: test that editing a submission, autofill userID is not overwritten
    Given the following "courses" exist:
      | fullname                 | shortname | category | groupmode |
      | Course divided in groups | C1        | 0        | 0         |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
      | Group 2 | C1 | G2 |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | teacher  | teacher1@asd.com |
      | student1 | student1  | user1    | student1@asd.com |
      | student2 | student2  | user2    | student2@asd.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "permission overrides" exist:
      | capability                          | permission | role    | contextlevel | reference |
      | mod/surveypro:editownsubmissions    | Allow      | student | Course       | C1        |
      | mod/surveypro:seeotherssubmissions  | Allow      | student | Course       | C1        |
      | mod/surveypro:editotherssubmissions | Allow      | student | Course       | C1        |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student2 | G1 |

    And I log in as "teacher1"
    And I follow "Course divided in groups"
    And I turn editing mode on
    And I add a "Surveypro" to section "1" and I fill the form with:
      | Survey name | Simple test                                                                               |
      | Description | This is a surveypro to test that editing a submission, autofill userID is not overwritten |
      | Group mode | Separate groups                                                                            |
    And I follow "Simple test"

    And I set the field "plugin" to "Autofill"
    And I press "Add"

    And I expand all fieldsets
    And I set the following fields to these values:
      | Content             | Your user ID |
      | Indent              | 0            |
      | Question position   | left         |
      | Element number      | 1            |
      | id_element01_select | user ID      |
    And I press "Add"

    And I set the field "plugin" to "Boolean"
    And I press "Add"

    And I expand all fieldsets
    And I set the following fields to these values:
      | Content           | Is this true? |
      | Required          | 1             |
      | Indent            | 0             |
      | Question position | left          |
      | Element number    | 2             |
    And I press "Add"

    And I log out

    # student1 logs in
    When I log in as "student1"
    And I follow "Course divided in groups"
    And I follow "Simple test"

    And I press "Add a response"

    # student1 submits his first response
    And I set the following fields to these values:
      | 2: Is this true? | Yes |
    And I press "Submit"

    And I press "Let me add one more response, please"
    And I press "Add a response"

    # student1 submits his second response
    And I set the following fields to these values:
      | 2: Is this true? | No |
    And I press "Submit"

    And I log out

    # student2 logs in
    When I log in as "student2"
    And I follow "Course divided in groups"
    And I follow "Simple test"

    And I follow "Responses"
    And I follow "edit_submission_1"
    Then I should see "4"
    Then I should see "Yes"

    And I set the following fields to these values:
      | 2: Is this true? | No |
    And I press "Submit"

    And I log out

    # student1 logs in
    When I log in as "student1"
    And I follow "Course divided in groups"
    And I follow "Simple test"

    And I follow "Responses"
    And I follow "edit_submission_1"
    # Then I should see "4" in the "felement fstatic" "css"
    # Then I should see "No" in the "id_surveypro_field_boolean_2" "css"
    Then I should see "4"
    Then I should see "No"

    And I log out

    # teacher1 logs in
    When I log in as "teacher1"
    And I follow "Course divided in groups"
    And I follow "Simple test"

    And I follow "Responses"
    And I follow "edit_submission_1"
    Then I should see "4"
    Then I should see "No"
    And I set the following fields to these values:
      | 2: Is this true? | Yes |
    And I press "Submit"

    And I log out

    # student1 logs in
    When I log in as "student1"
    And I follow "Course divided in groups"
    And I follow "Simple test"

    And I follow "Responses"
    And I follow "edit_submission_1"
    Then I should see "4"
    Then I should see "Yes"

    And I log out
