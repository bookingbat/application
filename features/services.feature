Feature: As an admin
  I need to be able to manage the services I offer
  So that my clients can book appointments for those services

  Scenario: I add a service
    Given I am logged in as admin
    And I am on "/services/manage"
    When I follow "New Service"
    Then I should be on "/services/new"
    And I should see "Service Name"
    When I fill in "name" with "training"
    And press "save"
    Then I should be on "/services/manage"
    And I should see "Service Created"
    And I should see "training"