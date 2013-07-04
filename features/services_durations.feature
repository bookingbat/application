Feature: As an admin
  I need to be able to set my service's duration(s)
  So that client's book appointments of the correct length(s)

  Scenario: I add a service with 30m duration only
    Given I am logged in as admin
    And I am on "/manage-services"
    When I follow "New Service"
    And I fill in "name" with "training"
    And I check "durations-30"
    When I press "save"
    And I follow "btn-edit" for "training"
    Then the "durations-30" checkbox should be checked

  Scenario: I update an existing service and add 1hr duration
    Given I am logged in as admin
    And I am on "/manage-services"
    When I follow "New Service"
    And I fill in "name" with "training"
    And I check "durations-30"
    And I press "save"
    And I follow "btn-edit" for "training"
    And I check "durations-60"
    When I press "save"
    And I follow "btn-edit" for "training"
    Then the "durations-30" checkbox should be checked
    And the "durations-60" checkbox should be checked