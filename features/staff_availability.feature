Feature: As an admin
  I need to be able to set up my staff's availability
  So that the staff doesn't have to login

  Scenario: I assign the staff's availability
    Given I have a staff "staff"
    And I am logged in as admin
    And I am on "/user/manage"
    When I follow "btn-availability" for "staff"
    And I fill in the following:
      |day  |7    |
      |start|01:00|
      |end  |05:30|
    And I press "submit"
    Then I should see "Added Availability"
    And I should see "01:00am to 05:30am"