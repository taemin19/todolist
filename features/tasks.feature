Feature: Tasks
  In order to manage the tasks on the application
  As a user
  I need to be able to create/view/edit/delete/mark them

  @loginAsUserNick
  Scenario: Create
    Given I am on "/tasks/create"
    And I should see 0 tasks
    When I fill in "task_title" with "Thor"
    And I fill in "task_content" with "Must defeat Loki"
    And I press "Ajouter"
    Then I should be on "/tasks"
    And I should see "Superbe ! La tâche a bien été ajoutée."
    And I should see 1 tasks
    And I should see "Thor"
    And I should see "Must defeat Loki"

  @loginAsUserNick
  Scenario: View
    Given the following tasks exist:
      | title    | content            |
      | Thor     | Must defeat Loki   |
      | Iron Man | Must defeat Ultron |
    And I am on "/tasks"
    Then I should see 2 tasks
    And I should see "Thor"
    And I should see "Must defeat Loki"
    And I should see "Iron Man"
    And I should see "Must defeat Ultron"

  @loginAsUserNick
  Scenario Outline: Edit
    Given the following tasks exist:
      | title | content          |
      | Thor  | Must defeat Loki |
    And I am on "/tasks/1/edit"
    When I fill in "task_title" with "<title>"
    And I fill in "task_content" with "<content>"
    And I press "Modifier"
    Then I should be on "/tasks"
    And I should see "Superbe ! La tâche a bien été modifiée."
    And I should see "<title>"
    And I should see "<content>"

    Examples:
      | title    | content            |
      | Iron Man | Must defeat Loki   |
      | Thor     | Must defeat Ultron |

  @loginAsUserNick
  Scenario: Delete
    Given the following tasks exist:
      | title    | content            |
      | Thor     | Must defeat Loki   |
      | Iron Man | Must defeat Ultron |
    And I am on "/tasks"
    And I should see 2 tasks
    When I press the 2nd "Supprimer" button
    Then I should see "Superbe ! La tâche a bien été supprimée."
    And I should see 1 tasks
    And I should not see "Iron Man"
    But I should see "Thor"

  @loginAsUserNick
  Scenario: Mark as done and mark as not done
    Given the following tasks exist:
      | title    | content            |
      | Thor     | Must defeat Loki   |
    And I am on "/tasks"
    And I should see 1 tasks as not done
    When I press "Marquer comme faite"
    Then I should see "Superbe ! La tâche Thor a bien été marquée comme faite."
    And I should see 1 tasks as done
    When I press "Marquer non terminée"
    Then I should see "Superbe ! La tâche Thor a bien été marquée comme non terminée."
    And I should see 1 tasks as not done

  @loginAsUserNick
  Scenario Outline: Throw some error messages when the task creation failed
    And I am on "/tasks/create"
    When I fill in "task_title" with "<title>"
    And I fill in "task_content" with "<content>"
    And I press "Ajouter"
    Then I should see "<error message>"

    Examples:
      | error message                 | title     | content            |
      | Vous devez saisir un titre.   |           | Must defeat Thanos |
      | Vous devez saisir du contenu. | Nick Fury |                    |

  @loginAsUserNick
  Scenario Outline: Throw some error messages when the task modification failed
    Given the following tasks exist:
      | title        | content            |
      | Nick Fury    | Must defeat Thanos |
    And I am on "/tasks/1/edit"
    When I fill in "task_title" with "<title>"
    And I fill in "task_content" with "<content>"
    And I press "Modifier"
    Then I should see "<error message>"

    Examples:
      | error message                 | title     | content            |
      | Vous devez saisir un titre.   |           | Must defeat Thanos |
      | Vous devez saisir du contenu. | Nick Fury |                    |
