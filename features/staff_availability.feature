Feature: As an admin
  I need to be able to set up my staff's availability
  So that the staff doesn't have to login

  Scenario: I assign the staff's availability
    Given I have a staff "staff"
    Given I am logged in as admin
    Given I am on "/user/manage"
    When I follow "btn-availability" for "staff"
    Then the url should match "/availability/staff/[0-9]+"
    Then I fill in "day" with "7"
    Then I fill in "start" with "01:00"
    Then I fill in "end" with "05:30"
    When I press "submit"
    Then I should see "Added Availability"
    And I should see "01:00am to 05:30am"