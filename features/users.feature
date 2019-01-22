Feature: Users
  In order to manage the users on the application
  As a user
  I need to be able to create/list/edit them

  Scenario: Create a user
    Given I am on "/users/create"
    When I fill in "user_username" with "nick"
    And I fill in "user_password_first" with "shield"
    And I fill in "user_password_second" with "shield"
    And I fill in "user_email" with "nick@fury.com"
    And I press "Ajouter"
    Then I should be on "/users"
    And I should see "Superbe ! L'utilisateur a bien été ajouté"
    And I should see 1 users
    And I should see "nick"
    And I should see "nick@fury.com"

  Scenario: List the users
    Given the following users exist:
      | username | password | email          |
      | nick     | shield   | nick@fury.com  |
      | tony     | ironman  | tony@stark.com |
    And I am on "/users"
    Then I should see 2 users
    And I should see "nick"
    And I should see "nick@fury.com"
    And I should see "tony"
    And I should see "tony@stark.com"

  Scenario Outline: Edit a user
    Given the following users exist:
      | username | password | email         |
      | nick     | shield   | nick@fury.com |
    And I am on "/users/1/edit"
    When I fill in "user_username" with "<username>"
    And I fill in "user_password_first" with "<password_first>"
    And I fill in "user_password_second" with "<password_second>"
    And I fill in "user_email" with "<email>"
    And I press "Modifier"
    Then I should be on "/users"
    And I should see "Superbe ! L'utilisateur a bien été modifié"
    And I should see "<username>"
    And I should see "<email>"

    Examples:
      | username | password_first | password_second | email            |
      | avengers | shield         | shield          | nick@fury.com    |
      | nick     | avengers       | avengers        | nick@fury.com    |
      | nick     | shield         | shield          | the@avengers.com |

  Scenario Outline: Throw some error messages when the user creation failed
    Given the following users exist:
      | username | password | email         |
      | nick     | shield   | nick@fury.com |
    And I am on "/users/create"
    When I fill in "user_username" with "<username>"
    And I fill in "user_password_first" with "<password_first>"
    And I fill in "user_password_second" with "<password_second>"
    And I fill in "user_email" with "<email>"
    And I press "Ajouter"
    Then I should see "<error message>"

    Examples:
      | error message                                | username | password_first | password_second | email            |
      | Vous devez saisir un nom d'utilisateur.      |          | avengers       | avengers        | the@avengers.com |
      | Ce nom d'utilisateur est déjà utilisé.       | nick     | avengers       | avengers        | the@avengers.com |
      | Vous devez saisir une adresse email.         | avengers | avengers       | avengers        |                  |
      | Le format de l'adresse n'est pas correcte.   | avengers | avengers       | avengers        | the@avengers     |
      | Cet e-mail est déjà utilisé.                 | avengers | avengers       | avengers        | nick@fury.com    |
      | Vous devez saisir un mot de passe.           | avengers |                |                 | the@avengers.com |
      | Les deux mots de passe doivent correspondre. | avengers | avengers       | shield          | the@avengers.com |


  Scenario Outline: Throw some error messages when the user modification failed
    Given the following users exist:
      | username | password | email          |
      | nick     | shield   | nick@fury.com  |
      | tony     | ironman  | tony@stark.com |
    And I am on "/users/1/edit"
    When I fill in "user_username" with "<username>"
    And I fill in "user_password_first" with "<password_first>"
    And I fill in "user_password_second" with "<password_second>"
    And I fill in "user_email" with "<email>"
    And I press "Modifier"
    Then I should see "<error message>"

    Examples:
      | error message                                | username | password_first | password_second | email          |
      | Vous devez saisir un nom d'utilisateur.      |          | shield         | shield          | nick@fury.com  |
      | Vous devez saisir une adresse email.         | nick     | shield         | shield          |                |
      | Le format de l'adresse n'est pas correcte.   | nick     | shield         | shield          | nick@fury      |
      | Cet e-mail est déjà utilisé.                 | nick     | shield         | shield          | tony@stark.com |
      | Vous devez saisir un mot de passe.           | nick     |                |                 | nick@fury.com  |
      | Les deux mots de passe doivent correspondre. | nick     | avengers       | shield          | nick@fury.com  |
