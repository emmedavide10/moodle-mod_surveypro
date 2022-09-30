@mod @mod_surveypro @surveyprofield @surveyprofield_radiobutton @current
Feature: test radiobutton selection
  In order to test radiobutton selection
  As teacher1
  I try to fill a radio button

  @javascript
  Scenario: test a submission works fine for radio button item
    Given the following "courses" exist:
      | fullname                    | shortname        | category |
      | Radiobutton submission test | Radiobutton test | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | teacher  | teacher1@nowhere.net |
      | student1 | Student1  | user1    | student1@nowhere.net |
    And the following "course enrolments" exist:
      | user     | course           | role           |
      | teacher1 | Radiobutton test | editingteacher |
      | student1 | Radiobutton test | student        |
    And the following "activities" exist:
      | activity  | name             | intro                                   | course           |
      | surveypro | Radiobutton test | To test submission of radiobutton item  | Radiobutton test |
    And I log in as "teacher1"
    And I am on "Radiobutton submission test" course homepage
    And I follow "Radiobutton test"

    And I set the field "typeplugin" to "Radio buttons"
    And I press "Add"

    And I expand all fieldsets
    And I set the following fields to these values:
      | Content           | Choose a direction |
      | Required          | 1                  |
      | Indent            | 0                  |
      | Question position | left               |
      | Element number    | 12a                |
      | Adjustment        | vertical           |
    And I set the multiline field "Options" to "North\nEast\nSouth\nWest"
    And I press "Add"

    And I set the field "typeplugin" to "Radio buttons"
    And I press "Add"

    And I expand all fieldsets
    And I set the following fields to these values:
      | Content           | Choose a vowel |
      | Required          | 1              |
      | Indent            | 0              |
      | Question position | left           |
      | Element number    | 12b            |
      | Adjustment        | horizontal     |
    And I set the multiline field "Options" to "a\nu\no\ni\ne"
    And I press "Add"

    And I log out

    # student1 logs in
    When I log in as "student1"
    And I am on "Radiobutton submission test" course homepage
    And I follow "Radiobutton test"
    And I press "New response"

    And I set the following fields to these values:
      | id_surveypro_field_radiobutton_1_1 | 1 |
      | id_surveypro_field_radiobutton_2_2 | 1 |

    And I press "Submit"

    And I press "Continue to responses list"
    Then I should see "1" submissions
