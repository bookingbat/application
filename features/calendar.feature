Feature: As a client
  I should have a calendar
  So I can visualize the availability for my appointment

  Scenario: I visit the calendar URL without selecting a service
    Given I am on "/calendar"
    Then I should see "You must pick a service first"
    And the response should not contain "calendar"