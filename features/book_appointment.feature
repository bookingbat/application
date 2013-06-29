Feature: As a client
  I need to book appointments
  So the business can provide some service for me

  Scenario: Booking an appointment
    And I have a staff "staff"
    And I have an admin "admin" with password "admin123"
    And I have a service "training"
    And the service "training" is assigned to "staff"
    And the staff "staff" has the following availability:
      | Mondays | 01:00 | 02:00 |
    And I am on "/services"

    When I follow "training"
    And I follow "btn-next-month"
    And I follow "Book an appointment!"
    Then the url should match "/make-booking/.*"

    Given I fill in "60" for "appointment_duration"
    When I press "Next"
    Then I should see 1 "#time option" elements
    And the "#time" element should contain "01:00 am"