@mod @mod_checklist @checklist
Feature: A student can update their progress in a checklist

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@asd.com |
      | student1 | Student   | 1        | student1@asd.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a checklist activity to course "Course 1" section 1 and I fill the form with:
      | Checklist                    | Test checklist      |
      | Introduction                 | This is a checklist |
      | Updates by                   | Student only        |
      | User can add their own items | Yes                 |
    And the following items exist in checklist "Test checklist":
      | text                      | required |
      | Checklist required item 1 | required |
      | Checklist required item 2 | required |
      | Checklist required item 3 | required |
      | Checklist optional item 4 | optional |
      | Checklist optional item 5 | optional |
    And I log out

  @javascript
  Scenario: When a student ticks/unticks items on a checklist their progress is updated
    When I am on the "Test checklist" "checklist activity" page logged in as "student1"
    Then I should see "0%" in the "#checklistprogressrequired" "css_element"
    And I should see "0%" in the "#checklistprogressall" "css_element"
    # Tick item 2.
    When I click on "Checklist required item 2" "text"
    Then I should see "33%" in the "#checklistprogressrequired" "css_element"
    And I should see "20%" in the "#checklistprogressall" "css_element"
    # Tick item 3.
    When I click on "Checklist required item 3" "text"
    Then I should see "67%" in the "#checklistprogressrequired" "css_element"
    And I should see "40%" in the "#checklistprogressall" "css_element"
    # Untick item 2.
    When I click on "Checklist required item 2" "text"
    Then I should see "33%" in the "#checklistprogressrequired" "css_element"
    And I should see "20%" in the "#checklistprogressall" "css_element"
    # Untick item 3.
    When I click on "Checklist required item 3" "text"
    Then I should see "0%" in the "#checklistprogressrequired" "css_element"
    And I should see "0%" in the "#checklistprogressall" "css_element"
    # Tick item 4.
    When I click on "Checklist optional item 4" "text"
    Then I should see "0%" in the "#checklistprogressrequired" "css_element"
    And I should see "20%" in the "#checklistprogressall" "css_element"
    # Delay to allow AJAX to clear before resetting DB (avoids test-failing popup).
    And I wait "2" seconds

  @javascript
  Scenario: When a student updates their progress and then returns to the page their progress is remembered
    Given I am on the "Test checklist" "checklist activity" page logged in as "student1"
    When I click on "Checklist required item 1" "text"
    And I click on "Checklist required item 3" "text"
    And I click on "Checklist optional item 4" "text"
    # Make sure the AJAX request has finished.
    And I wait "2" seconds
    And I am on the "Test checklist" "checklist activity" page
    Then the following fields match these values:
      | Checklist required item 1 | 1 |
      | Checklist required item 2 | 0 |
      | Checklist required item 3 | 1 |
      | Checklist optional item 4 | 1 |
      | Checklist optional item 5 | 0 |
    # Note - the rounding here is inconsistent between JS & PHP, but I am cautious about fixing it.
    And I should see "66%" in the "#checklistprogressrequired" "css_element"
    And I should see "60%" in the "#checklistprogressall" "css_element"

  @javascript
  Scenario: When a student updates their progress then the teacher can see that progress
    Given I am on the "Test checklist" "checklist activity" page logged in as "student1"
    When I click on "Checklist required item 1" "text"
    And I click on "Checklist required item 3" "text"
    And I click on "Checklist optional item 4" "text"
    # Make sure the AJAX request has finished.
    And I wait "2" seconds
    And I log out
    And I am on the "Test checklist" "checklist activity" page logged in as "teacher1"
    When I follow "View progress"
    Then ".level0-checked.c1" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c3" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c4" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c2" "css_element" should not exist in the "Student 1" "table_row"
    And ".level0-checked.c5" "css_element" should not exist in the "Student 1" "table_row"

  @javascript
  Scenario: Checklists can mark themselves as complete.
    Given I log in as "admin"
    And I set the following administration settings values:
      | enablecompletion | 1 |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Enable completion tracking" to "Yes"
    And I press "Save and display"
    And I am on the "Test checklist" "checklist activity" page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I enable automatic completion tracking for the checklist
    And I set the field "completionusegrade" to "1"
    And I set the field "completionpercentenabled" to "1"
    And I set the field "completionpercent" to "100"
    And I press "Save and return to course"
    And "Student 1" user has not completed "Test checklist" activity
    And I log out
    When I am on the "Test checklist" "checklist activity" page logged in as "student1"
    And I click on "Checklist required item 1" "text"
    And I click on "Checklist required item 2" "text"
    And I click on "Checklist required item 3" "text"
    And I should see "100%" in the "#checklistprogressrequired" "css_element"
    And I wait "2" seconds
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    Then "Student 1" user has completed "Test checklist" activity

  @javascript
  Scenario: Students can add and update their own items
    Given I am on the "Test checklist" "checklist activity" page logged in as "student1"
    When I press "Add your own items"
    And I click on "Add a new item to the list" "link" in the "Checklist required item 2" "list_item"
    And I set the following fields to these values:
      | displaytext     | Custom item 1    |
      | displaytextnote | Some explanation |
    And I press "Add"
    And I set the following fields to these values:
      | displaytext     | Custom item 2    |
      | displaytextnote | More explanation |
    And I press "Add"
    And I press "Stop adding your own items"
    Then I should see "Custom item 1"
    And I should see "Some explanation"
    And I should see "Custom item 2"
    And I should see "More explanation"

    When I click on "Custom item 1" "text"
    Then I should see "14%" in the "#checklistprogressall" "css_element"
    And I should see "0%" in the "#checklistprogressrequired" "css_element"

    When I wait "1" seconds
    And I reload the page
    Then the following fields match these values:
      | Custom item 1 | 1 |
      | Custom item 2 | 0 |
    And I should see "14%" in the "#checklistprogressall" "css_element"
    And I should see "0%" in the "#checklistprogressrequired" "css_element"
