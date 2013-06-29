Feature: As an admin
  I need to be able to assign the services my staff performs
  So that clients can book appointments


  Scenario: I have a service & assign it to a staff
    Given I have a staff "staff"
    And I am logged in as admin
    And I am on "/user/manage"
    And I have a service "training"
    When I follow "btn-services" for "staff"
    Then the url should match "/services/assign/staff/[0-9]+"
    And the "services-1" checkbox should not be checked
    When I check "services-1"
    And I press "save"
    Then I should be on "/user/manage"
    And I should see "Staff's services updated"
    When I follow "btn-services" for "staff"
    Then the "services-1" checkbox should be checked

  Scenario: I have multiple services & assign only some of them to a staff
    Given I have a staff "staff"
    And I am logged in as admin
    And I am on "/user/manage"
    And I have a service "training"
    And I have a service "massage"
    When I follow "btn-services" for "staff"
    Then the url should match "/services/assign/staff/[0-9]+"
    And the "services-1" checkbox should not be checked
    And the "services-2" checkbox should not be checked
    When I check "services-2"
    And I press "save"
    Then I should be on "/user/manage"
    And I should see "Staff's services updated"
    When I follow "btn-services" for "staff"
    Then the "services-1" checkbox should not be checked
    When I check "services-2"
    Then the "services-2" checkbox should be checked

  Scenario: I unassign a service from a staff
    Given I have a staff "staff"
    And I am logged in as admin
    And I am on "/user/manage"
    And I have a service "training"
    And the service "training" is assigned to "staff"
    When I follow "btn-services" for "staff"
    Then the "services-1" checkbox should be checked
    When I uncheck "services-1"
    And I press "save"
    And I follow "btn-services" for "staff"
    Then the "services-1" checkbox should not be checked