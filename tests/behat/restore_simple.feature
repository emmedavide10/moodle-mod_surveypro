@mod @mod_surveypro
Feature: Simple restore of a surveypro
  In order to test simple restore procedure
  As a teacher
  I make a simple restore of a course with two instances of surveypro

  @javascript @_file_upload
  Scenario: Restore the demo course
    Given I log in as "admin"
    Given I navigate to "Courses > Restore course" in site administration
    And I upload "mod/surveypro/tests/fixtures/demo_course-20160108.mbz" file to "Files" filemanager
    And I press "Restore"
    And I click on "Continue" "button"
    And I set the field "restore-category-1" to "1"
    And I click on "Continue" "button"
    And I click on "Next" "button"
    And I click on "Next" "button"
    And I click on "Perform restore" "button"
    And I click on "Continue" "button"
    Then I should see "\"age\" element"
    Then I should see "\"attachment\" element"
    Then I should see "\"autofill\" element"
    Then I should see "\"boolean\" element"
    Then I should see "\"checkbox\" element"
    Then I should see "\"date\" element"
    Then I should see "\"date (short)\" element"
    Then I should see "\"date and time\" element"
    Then I should see "\"integer\" element"
    Then I should see "\"multiselect\" element"
    Then I should see "\"numeric\" element"
    Then I should see "\"radio button\" element"
    Then I should see "\"rate\" element"
    Then I should see "\"recurrence\" element"
    Then I should see "\"select\" element"
    Then I should see "\"text area\" element"
    Then I should see "\"text (short)\" element"
    Then I should see "\"time\" element"
    Then I should see "Examples of parent-child relations"
    Then I should see "General example of use of this module"
