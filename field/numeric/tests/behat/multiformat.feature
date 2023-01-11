@mod @mod_surveypro @surveyprofield @surveyprofield_numeric
Feature: verify the input with different number format
  In order to verify numbers are correctly handled in different languages // Why this feature is useful
  As student1                                                             // It can be 'an admin', 'a teacher', 'a student', 'a guest', 'a user', 'a tests writer' and 'a developer'
  I submit a numeric field                                                // The feature we want

  Background:
    Given remote langimport tests are enabled
    And the following "courses" exist:
      | fullname                  | shortname | category | groupmode |
      | Multiformat numeric input | MF Number | 0        | 0         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student1  | user1    | student1@nowhere.net |
    And the following "course enrolments" exist:
      | user     | course    | role           |
      | student1 | MF Number | student        |
    And the following "activities" exist:
      | activity  | name                           | intro                          | course    |
      | surveypro | Test multiformat numeric input | Test multiformat numeric input | MF Number |
    And surveypro "Test multiformat numeric input" contains the following items:
      | type   | plugin  |
      | field  | numeric |

  @javascript
  Scenario: submit the numeric field using two different formats
    Given I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    And I set the field "menupack" to "Italiano"
    And I press "Install selected language pack(s)"
    Then I should see "Language pack 'it' was successfully installed"
    And I log out

    # Force English for UI.
    And I log in as "student1"
    And I am on site homepage
    And I follow "Multiformat numeric input"
    And I follow "Test multiformat numeric input"
    And I press "New response"
    And I set the field "Write the best approximation of π you can remember" to "3,14"
    And I press "Submit"
    Then I should see "Provided value is not a number"

    And I set the field "Write the best approximation of π you can remember" to "3.14"
    And I press "Submit"

    And I follow "Preference" in the user menu
    And I follow "Preferred language"
    And I set the field "Preferred language" to "Italiano ‎(it)‎"
    And I press "Save changes"

    And I am on site homepage
    And I follow "Multiformat numeric input"
    And I follow "Test multiformat numeric input"
    And I press "Nuova risposta"
    And I set the field "Write the best approximation of π you can remember" to "3.14"
    And I press "Invia"
    Then I should see "Il valore fornito non è un numero"

    And I set the field "Write the best approximation of π you can remember" to "3,14"
    And I press "Invia"

    And I press "Mostra la lista"
    And I follow "view_submission_row_1"
    Then the field "Write the best approximation of π you can remember" matches value "3,14"

    And I follow "Raccolta dati"
    And I follow "view_submission_row_2"
    Then the field "Write the best approximation of π you can remember" matches value "3,14"

    And I follow "Preferenze" in the user menu
    And I follow "Preferenze lingua"
    And I set the field "Preferenze lingua" to "English ‎(en)‎"
    And I press "Salva modifiche"

    And I am on site homepage
    And I follow "Multiformat numeric input"
    And I follow "Test multiformat numeric input"

    And I follow "Responses"
    And I follow "view_submission_row_1"
    Then the field "Write the best approximation of π you can remember" matches value "3.14"

    And I follow "Responses"
    And I follow "view_submission_row_2"
    Then the field "Write the best approximation of π you can remember" matches value "3.14"
