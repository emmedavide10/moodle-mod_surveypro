@mod @mod_surveypro
Feature: verify the deletion of old items works as expected during mastertemplates replacement
  In order to verify the overwrite of mastertemplates
  As a teacher
  I need apply mastertemplates one over the previous

  @javascript
  Scenario: deletion of old items works as expected when apply mastertemplates
    Given the following "courses" exist:
      | fullname                 | shortname                | category | groupmode |
      | Overwrite mastertemplate | Overwrite mastertemplate | 0        | 0         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | t        | teacher1@nowhere.net |
    And the following "course enrolments" exist:
      | user     | course                   | role           |
      | teacher1 | Overwrite mastertemplate | editingteacher |
    And the following "activities" exist:
      | activity  | name                         | intro                               | course                   |
      | surveypro | To overwrite mastertemplates | To test overwrite of mastertemplate | Overwrite mastertemplate |
    And I log in as "teacher1"
    And I am on "Overwrite mastertemplate" course homepage
    And I follow "To overwrite mastertemplates"

    And I set the field "mastertemplate" to "ATTLS (20 item version)"
    And I press "Apply"
    Then I should see "Attitudes Towards Thinking and Learning"

    And I follow "Elements" page in tab bar
    And I press "Yes"

    When I am on the "To overwrite mastertemplates" "mod_surveypro > Master templates Apply" page
    And I set the field "mastertemplate" to "COLLES (Preferred and Actual)"
    And I press "Apply"
    Then I should see "I prefer that my learning focuses on issues that interest me."
    Then I should see "I found that my learning focuses on issues that interest me."

    And I follow "Elements" page in tab bar
    And I press "Yes"

    When I am on the "To overwrite mastertemplates" "mod_surveypro > Master templates Apply" page
    And I set the field "mastertemplate" to "COLLES (Actual)"
    And I press "Apply"
    Then I should see "In this online unit"
    Then I should see "my learning focuses on issues that interest me"

    And I follow "Elements" page in tab bar
    And I press "Yes"

    When I am on the "To overwrite mastertemplates" "mod_surveypro > Master templates Apply" page
    And I set the field "mastertemplate" to "COLLES (Preferred)"
    And I press "Apply"
    Then I should see "In this online unit"
    Then I should see "my learning focuses on issues that interest me"

    And I follow "Elements" page in tab bar
    And I press "Yes"

    When I am on the "To overwrite mastertemplates" "mod_surveypro > Master templates Apply" page
    And I set the field "mastertemplate" to "Critical Incidents"
    And I press "Apply"
    Then I should see "While thinking about recent events in this class, answer the questions below."
