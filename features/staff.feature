Feature: As an admin
  I need to be able to manage my staff, as well as set their availability & services
  So that my clients can book appoinments

  Scenario: I create a staff
    Given I am logged in as admin
    And I follow "Manage Staff"
    And I follow "New User"
    When I fill in the following:
      | username    | staff               |
      | first_name  | john                |
      | last_name   | doe                 |
      | email       | staff@example.com   |
      | phone       | 0000000000          |
      | password    | staff123            |
      | verifypassword |  staff123        |
    And I press "submit"
    Then I should be on "/user/manage"
    Then I should see "Created user"