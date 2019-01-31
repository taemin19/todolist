Feature: Navigation
  In order to navigate between the pages on the application
  As a user
  I need to be able to see some links and click on them

  @loginAsUserNick
  Scenario: Homepage link
    Given I am on "/tasks"
    When I click "To Do List app"
    Then I should be on "/"

  @loginAsUserNick
  Scenario: Logout link
    Given I am on "/"
    When I click "Se déconnecter"
    Then I should be on "/login"

  @loginAsAdminShield
  Scenario: User create link
    Given I am on "/"
    When I click "Créer un utilisateur"
    Then I should be on "/admin/users/create"

  @loginAsAdminShield
  Scenario: User edit link
    Given the following users exist:
      | username | password | email          |
      | nick     | shield   | nick@fury.com  |
    Given I am on "/admin/users"
    When I click "Edit"
    Then I should be on "admin/users/1/edit"

  @loginAsUserNick
  Scenario Outline: Task create link
    Given I am on "<url>"
    When I click "<link>"
    Then I should be on "/tasks/create"

    Examples:
      | link                     | url    |
      | Créer une nouvelle tâche | /      |
      | Créer une tâche          | /tasks |

  @loginAsUserNick
  Scenario Outline: Task list link
    Given I am on "<url>"
    When I click "<link>"
    Then I should be on "/tasks"

    Examples:
      | link                                  | url           |
      | Consulter la liste des tâches à faire | /             |
      | Retour à la liste des tâches          | /tasks/create |

  @loginAsUserNick
  Scenario: Task list link
    Given I am on "/"
    When I click "Consulter la liste des tâches terminées"
    Then I should be on "/tasks/done"

  @loginAsUserNick
  Scenario: Task list as done link
    Given I am on "/"
    When I click "Consulter la liste des tâches terminées"
    Then I should be on "/tasks/done"
