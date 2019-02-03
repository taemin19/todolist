Feature: Navigation
  In order to navigate between the pages on the application
  As a user
  I need to be able to see some links and click on them

  Scenario Outline: Homepage link
    Given I am on "<url>"
    When I click "To Do List app"
    Then I should be on "/"

  Examples:
    | url    |
    | /      |
    | /login |

  @loginAsUserNick
  Scenario Outline: Homepage link
    Given the following tasks exist:
      | title    | content            | isDone |
      | Thor     | Must defeat Loki   | 0      |
    And I am on "<url>"
    When I click "To Do List app"
    Then I should be on "/"

    Examples:
      | url           |
      | /             |
      | /tasks/       |
      | /tasks/done   |
      | /tasks/create |
      | /tasks/1/edit |

  @loginAsAdminShield
  Scenario Outline: Homepage link
    Given the following tasks exist:
      | title    | content            | isDone |
      | Thor     | Must defeat Loki   | 0      |
    And I am on "<url>"
    When I click "To Do List app"
    Then I should be on "/"

    Examples:
      | url                 |
      | /                   |
      | /tasks/             |
      | /tasks/done         |
      | /tasks/create       |
      | /tasks/1/edit       |
      | /admin/users        |
      | /admin/users/create |
      | /admin/users/1/edit |

  @loginAsUserNick
  Scenario Outline: Tasks list link
    Given I am on "<url>"
    When I click "<link>"
    Then I should be on "/tasks/"

  Examples:
    | url           | link                                  |
    | /             | Consulter la liste des tâches à faire |
    | /tasks/create | Retour à la liste des tâches          |

  @loginAsUserNick
  Scenario: Tasks done list link
    Given I am on "/"
    When I click "Consulter la liste des tâches terminées"
    Then I should be on "/tasks/done"

  @loginAsUserNick
  Scenario Outline: Tasks create link
    Given I am on "<url>"
    When I click "<link>"
    Then I should be on "/tasks/create"

  Examples:
    | url                 | link                     |
    | /                   | Créer une nouvelle tâche |
    | /tasks/             | Créer une tâche          |
    | /tasks/done         | Créer une tâche          |

  @loginAsUserNick
  Scenario Outline: Tasks edit link
    Given the following tasks exist for current user:
      | title    | content            | isDone |
      | Thor     | Must defeat Loki   | 0      |
      | Thor     | Must defeat Loki   | 1      |
    And I am on "<url>"
    When I click "Thor"
    Then I should be on "/tasks/<id>/edit"

  Examples:
    | url         | id |
    | /tasks/     | 1  |
    | /tasks/done | 2  |

  @loginAsAdminShield
  Scenario: Users create link
    Given I am on "/"
    When I click "Créer un utilisateur"
    Then I should be on "/admin/users/create"

  @loginAsAdminShield
  Scenario: Users edit link
    Given the following users exist:
      | username | password | email          |
      | nick     | shield   | nick@fury.com  |
    Given I am on "/admin/users"
    When I click "Edit"
    Then I should be on "admin/users/1/edit"