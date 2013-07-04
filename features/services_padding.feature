Feature: As an admin
  I need to be able to specify a padding time for services
  So that my staff has enough time between appointments.

  Scenario: I add a service with no padding
    Given I am logged in as admin
    And I am on "/manage-services"
    When I follow "New Service"
    And I fill in "name" with "training"
    And I fill in "padding" with "0"
    And press "save"
    When I follow "btn-edit" for "training"
    Then field "padding" should have value "0"

  Scenario: I add a service with 30m padding
    Given I am logged in as admin
    And I am on "/manage-services"
    When I follow "New Service"
    And I fill in "name" with "training"
    And I fill in "padding" with "30"
    And press "save"
    When I follow "btn-edit" for "training"
    Then field "padding" should have value "30"

  Scenario: I add a service with 1hr padding
    Given I am logged in as admin
    And I am on "/manage-services"
    When I follow "New Service"
    And I fill in "name" with "training"
    And I fill in "padding" with "60"
    And press "save"
    When I follow "btn-edit" for "training"
    Then field "padding" should have value "60"

  Scenario: I edit a service's padding from 30 to 60
    Given I am logged in as admin
    And I am on "/manage-services"
    When I follow "New Service"
    And I fill in "name" with "training"
    And I fill in "padding" with "30"
    And press "save"
    And I follow "btn-edit" for "training"
    And I fill in "padding" with "60"
    And press "save"
    And I follow "btn-edit" for "training"
    Then field "padding" should have value "60"