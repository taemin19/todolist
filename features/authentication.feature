Feature: Authentication
  In order to authenticate on the application
  As a user
  I need to be able to login/logout

  Scenario Outline: Login
    Given the following users exist:
      | username | password | email          |
      | nick     | shield   | nick@fury.com  |
    Given the following admins exist:
      | username | password | email          |
      | shield   | avengers | the@avengers.com  |
    And I am on "/login"
    When I fill in "username" with "<username>"
    And I fill in "password" with "<password>"
    And I press "Se connecter"
    Then I should be on "/"
    And I should see "Se déconnecter"

    Examples:
      | username | password |
      | nick     | shield   |
      | shield   | avengers |

  @loginAsUserNick
  Scenario: Logout
    Given I am on "/"
    And I should see "Se déconnecter"
    When I click "Se déconnecter"
    Then I should be on "/"

  @loginAsAdminShield
  Scenario: Logout
    Given I am on "/"
    And I should see "Se déconnecter"
    When I click "Se déconnecter"
    Then I should be on "/"

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
