Feature: As a client
  I need to book appointments
  So the business can provide some service for me

  Scenario: I should see a list of services
    Given I have a service "training"
    And I have a service "massage"
    When I go to "/services"
    Then I should see "training"
    And I should see "massage"

  Scenario: When I pick a service, it should show the availability on the calendar
    Given I have a staff "staff"
    And I have a service "training"
    And the service "training" is assigned to "staff"
    And the staff "staff" has the following availability:
      | Mondays | 01:00 | 02:00 |
    And I am on "/services"
    When I follow "training"
    And I follow "btn-next-month"
    Then I should see "01:00 am to 02:00 am"
    And I should see "Book an appointment!"

  Scenario: When I pick a day off the calendar, I should see the choice of appointment duration(s)
    Given I have a staff "staff"
    And I have a service "training"
    And the service "training" is assigned to "staff"
    And the staff "staff" has the following availability:
      | Mondays | 01:00 | 02:00 |
    When I follow "Book an appointment!"
    Then the url should match "/make-booking/.*"
    And I should see "Appointment Duration"
    And I should see 2 "#appointment_duration option" elements
    And the "#appointment_duration" element should contain "1 Hour"
    And the "#appointment_duration" element should contain "1.5 Hour"

  #Scenario: It should disable durations that don't fit in the window

  Scenario:  When I pick a duration, I should see possible start time(s)
    Given I have a staff "staff"
    And I have a service "training"
    And the service "training" is assigned to "staff"
    And the staff "staff" has the following availability:
      | Mondays | 01:00 | 02:00 |
    When I fill in "60" for "appointment_duration"
    When I press "Next"
    Then I should see 1 "#time option" elements
    And the "#time" element should contain "01:00 am"