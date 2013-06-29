Feature: As a client
  I need to book appointments
  So the business can provide some service for me

  Scenario: Booking an appointment
    Given I am logged in as a client
    And I have a staff "staff"
    And I have a service "training"
    And the staff "staff" provides the service "training"
    And the staff "staff" has the following availability:
      | Mondays | 01:00 | 02:00 |
    When I visit "/services"
    And I follow "training"
    Then I follow "Book an appointment!"
    Then the url should match "/make-booking/.*"