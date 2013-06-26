Feature: As an admin I should be able to manage my staff

  Scenario: I create a staff
    Given I am logged in as admin
    When I follow "btn-manage-staff"
    Then I should be on "/user/manage"
    When I follow "btn-new-user"
    Then I should be on "/user/register"
    When I fill in "username" with "staff"
    When I fill in "first_name" with "john"
    When I fill in "last_name" with "doe"
    When I fill in "email" with "staff@example.com"
    When I fill in "phone" with "0000000000"
    When I fill in "password" with "staff123"
    When I fill in "verifypassword" with "staff123"
    When I press "submit"
    Then I should be on "/user/manage"
    Then I should see "Created user"

  Scenario: I assign the staff's availability
    Given I have a staff "staff"
    Given I am logged in as admin
    Given I am on "/user/manage"
    When I follow "btn-availability" for user "staff"
    Then the url should match "/availability/staff/[0-9]+"
    Then I fill in "day" with "7"
    Then I fill in "start" with "01:00"
    Then I fill in "end" with "05:30"
    When I press "submit"
    Then I should see "Added Availability"
    And I should see "01:00am to 05:30am"