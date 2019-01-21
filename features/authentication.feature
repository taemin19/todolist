Feature: Authentication
  In order to authenticate on the application
  As a user
  I need to be able to login/logout

  Scenario: Login
    Given the following users exist:
      | username | password | email          |
      | nick     | shield   | nick@fury.com  |
    And I am on "/login"
    When I fill in "username" with "nick"
    And I fill in "password" with "shield"
    And I press "Se connecter"
    Then I should be on "/"
    And I should see "Se déconnecter"

  @loginAsUserNick
  Scenario: Logout
    Given I am on "/"
    And I should see "Se déconnecter"
    When I click "Se déconnecter"
    Then I should be on "/login"

  Scenario Outline: Throw error message when the user authentication failed
    Given the following users exist:
      | username | password | email          |
      | nick     | shield   | nick@fury.com  |
    And I am on "/login"
    When I fill in "username" with "<username>"
    And I fill in "password" with "<password>"
    And I press "Se connecter"
    Then I should be on "/login"
    And I should see "Invalid credentials"
    And the "username" field should contain "<username>"

    Examples:
      | username | password |
      | nick     | avengers |
      | thanos   | shield   |
