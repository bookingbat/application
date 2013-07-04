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
    Then I should be on "/manage-staff"
    And I should see "Created user"

  Scenario: I edit a staff
    Given I am logged in as admin
    And I have the following user:
      | type        | staff               |
      | username    | staff               |
      | first_name  | john                |
      | last_name   | doe                 |
      | email       | staff@example.com   |
      | phone       | 0000000000          |
      | password    | staff123            |
      | verifypassword |  staff123        |
    And I am on "/manage-staff"
    When I follow "btn-edit" for "staff"
    And I fill in the following:
      | username    | staff2               |
      | first_name  | john2                |
      | last_name   | doe2                 |
      | email       | staff2@example.com   |
      | phone       | 0000000002           |
    And I press "submit"
    Then I should see "User Updated"
    When I follow "btn-edit" for "staff2"
    Then the response should contain "staff2"
    And the response should contain "john2"
    And the response should contain "doe2"
    And the response should contain "staff2@example.com"
    And the response should contain "0000000002"