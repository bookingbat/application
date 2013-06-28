Feature: As an admin
  I need to be able to manage my staff, as well as set their availability & services
  So that my clients can book appoinments

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