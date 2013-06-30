Feature: As a client
  I should see a list of services as soon as I hit the homepage
  So that I can easily start booking an appointment

  Scenario: It should list a valid service
    Given I have a service "Training"
    And the service "Training" has the durations "60,90"
    When I go to "/"
    Then I should see "Pick A Service"
    And I should see "Training"

  Scenario: I should list multiple services
    Given I have a service "training"
    And the service "training" has the durations "60,90"
    And I have a service "massage"
    And the service "massage" has the durations "60,90"
    When I go to "/"
    Then I should see "training"
    And I should see "massage"

  Scenario: It should not list invalid services with no durations
    Given I have a service "Training"
    And the service "training" has the durations ""
    When I go to "/"
    Then I should see "Pick A Service"
    And I should not see "Training"