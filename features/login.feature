Feature: Logging in
  I need to be able to see the login page

  Scenario:
    Given I am on "/"
    When I follow "btn-login"
    Then I should be on "/user/login"
    And I should see text matching "Username"
    And I should see text matching "Password"