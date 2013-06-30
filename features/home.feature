Feature: As a client
  I should see a list of services as soon as I hit the homepage
  So that I can easily start booking an appointment

  Scenario:
    Given I have a service "Training"
    When I go to "/"
    Then I should see "Pick A Service"
    And I should see "Training"