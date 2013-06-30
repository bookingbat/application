Feature: As a client
  I should have a staff picker on the calendar
  So that I may choose to view availability for a particular staff

  Scenario: I should see staff for the service
    Given I have a staff "staff1"
    And I have a staff "staff2"
    And I have a service "service1"
    And the service "service1" is assigned to "staff1"
    And the service "service1" is assigned to "staff2"
    And I am on "/calendar/service/1"
    Then the response should contain "staff1"
    And the response should contain "staff2"

  Scenario: I should not see staff that aren't assigned to the service
    Given I have a staff "staff1"
    And I have a staff "staff2"
    And I have a service "service1"
    And the service "service1" is assigned to "staff1"

    And I am on "/calendar/service/1"
    Then the response should contain "staff1"
    And the response should not contain "staff2"