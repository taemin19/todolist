Feature: Users
  In order to manage the users on the application
  As a admin
  I need to be able to create/list/edit them

  @loginAsAdminShield
  Scenario Outline: Create a user
    Given I am on "admin/users/create"
    When I fill in "user_username" with "nick"
    And I fill in "user_password_first" with "shield"
    And I fill in "user_password_second" with "shield"
    And I fill in "user_email" with "nick@fury.com"
    And I select "<role>" from "user_roles_0"
    And I press "Ajouter"
    Then I should be on "/admin/users"
    And I should see "L'utilisateur a bien été ajouté"
    And I should see "nick"
    And I should see "nick@fury.com"
    And I should see "<role>"

    Examples:
      | role       |
      | ROLE_USER  |
      | ROLE_ADMIN |

  @loginAsAdminShield
  Scenario: List the users
    Given the following users exist:
      | username | password | email          |
      | nick     | shield   | nick@fury.com  |
    And I am on "/admin/users"
    Then I should see 2 users
    And I should see "nick"
    And I should see "nick@fury.com"
    And I should see "shield"
    And I should see "the@avengers.com"

  @loginAsAdminShield
  Scenario Outline: Edit a user
    Given the following users exist:
      | username | password | email         |
      | nick     | shield   | nick@fury.com |
    And I am on "/admin/users/2/edit"
    When I fill in "user_username" with "<username>"
    And I fill in "user_password_first" with "<password_first>"
    And I fill in "user_password_second" with "<password_second>"
    And I fill in "user_email" with "<email>"
    And I select "<role>" from "user_roles_0"
    And I press "Modifier"
    Then I should be on "/admin/users"
    And I should see "L'utilisateur a bien été modifié"
    And I should see "<username>"
    And I should see "<email>"
    And I should see "<role>"

    Examples:
      | username | password_first | password_second | email            | role       |
      | tony     | shield         | shield          | nick@fury.com    | ROLE_USER  |
      | tony     | ironman        | ironman         | nick@fury.com    | ROLE_USER  |
      | tony     | ironman        | ironman         | tony@stark.com   | ROLE_USER  |
      | tony     | ironman        | ironman         | tony@stark.com   | ROLE_ADMIN |

  @loginAsAdminShield
  Scenario Outline: Throw some error messages when the user creation failed
    Given the following users exist:
      | username | password | email         |
      | nick     | shield   | nick@fury.com |
    And I am on "/admin/users/create"
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

  @loginAsAdminShield
  Scenario Outline: Throw some error messages when the user modification failed
    Given the following users exist:
      | username | password | email          |
      | nick     | shield   | nick@fury.com  |
      | tony     | ironman  | tony@stark.com |
    And I am on "/admin/users/1/edit"
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
