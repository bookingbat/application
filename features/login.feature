Feature: Logging in
  I need to be able to see the login page

  Scenario:
    Given I am on "/"
    When I follow "btn-login"
    Then I should be on "/user/login"
    And I should see text matching "Username"
    And I should see text matching "Password"

  Scenario:
    Given I am on "/user/login"
    And I have a user "admin" with password "admin"
    When I fill in "username" with "admin"
    When I fill in "password" with "admin"
    When I press "login"
    Then I should be on "/calendar"