Feature: As an admin
  I need to be able to manage the services I offer
  So that my clients can book appointments for those services

  Scenario: I add a service
    Given I am logged in as admin
    And I am on "/manage-services"
    When I follow "New Service"
    Then I should be on "/new-service"
    And I should see "Service Name"
    When I fill in "name" with "training"
    And press "save"
    Then I should be on "/manage-services"
    And I should see "Service Created"
    And I should see "training"

  Scenario:  I edit a service's name
    Given I have a service "training"
    And I am on "/manage-services"
    Then I follow "btn-edit" for "training"
    Then the url should match "/services/edit/id/[0-9]+"
    When I fill in "name" with "training-renamed"
    Then press "save"
    Then I should be on "/manage-services"
    And I should see "Service Updated"
    And I should see "training-renamed"