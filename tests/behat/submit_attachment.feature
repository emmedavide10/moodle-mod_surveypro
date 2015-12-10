@mod @mod_surveypro
Feature: make a submission test for each available item
  In order to test that minimal use of surveypro is guaranteed
  As student1
  I add an attachment item, I fill it and I go to see responses

  @javascript
  Scenario: test a submission works fine for each available item
    Given the following "courses" exist:
      | fullname                            | shortname       | category |
      | Test submission for attachment item | Submission test | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | teacher  | teacher1@nowhere.net |
      | student1 | Student1  | user1    | student1@nowhere.net |
    And the following "course enrolments" exist:
      | user     | course          | role           |
      | teacher1 | Submission test | editingteacher |
      | student1 | Submission test | student        |

    And I log in as "teacher1"
    And I follow "Test submission for attachment item"
    And I turn editing mode on
    And I add a "Surveypro" to section "1" and I fill the form with:
      | Name        | Surveypro test                                            |
      | Description | This is a surveypro to test submission of attachment item |
    And I follow "Surveypro test"

    And I set the field "typeplugin" to "Attachment"
    And I press "Add"

    And I expand all fieldsets
    And I set the following fields to these values:
      | Content                  | Please upload your Curriculum Vitae |
      | Required                 | 1                                   |
      | Indent                   | 0                                   |
      | Question position        | left                                |
      | Element number           | 2                                   |
      | Hide filling instruction | 1                                   |
    And I press "Add"

    And I log out

    # student1 logs in
    When I log in as "student1"
    And I follow "Test submission for attachment item"
    And I follow "Surveypro test"
    And I press "New response"

    # student1 submits
    And I upload "mod/surveypro/tests/fixtures/dummyCV.pdf" file to "2: Please upload your Curriculum Vitae" filemanager

    And I press "Submit"

    And I press "Continue to responses list"
    Then I should see "1" submissions displayed