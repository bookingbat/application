Feature: It has a login system with admins & staff users

  Scenario: It should link me to the login page
    Given I am on "/"
    When I follow "btn-login"
    Then I should be on "/user/login"
    And I should see text matching "Username"
    And I should see text matching "Password"

  Scenario: It should deny a wrong password
    Given I am on "/user/login"
    And I have a user "admin" with password "admin123"
    When I fill in "username" with "admin"
    When I fill in "password" with "wrong-password"
    When I press "login"
    Then I should see "Invalid password or username not found"

  Scenario: I should log in as admin
    Given I am on "/user/login"
    And I have a user "admin" with password "admin123"
    When I fill in "username" with "admin"
    When I fill in "password" with "admin123"
    When I press "login"
    Then I should be on "/calendar"