Feature: Registration
  In order to create an account on the application
  As a user
  I need to be able to register

  Scenario: Create a user account
    Given I am on "register"
    When I fill in "user_register_username" with "nick"
    And I fill in "user_register_password_first" with "shield"
    And I fill in "user_register_password_second" with "shield"
    And I fill in "user_register_email" with "nick@fury.com"
    And I press "Créer votre compte ToDoList"
    Then I should be on homepage
    And I should see "Superbe ! Bienvenue Nick , votre compte a bien été crée."

  Scenario Outline: Throw some error messages when the user registration failed
    Given the following users exist:
      | username | password | email         |
      | nick     | shield   | nick@fury.com |
    And I am on "/register"
    When I fill in "user_register_username" with "<username>"
    And I fill in "user_register_password_first" with "<password_first>"
    And I fill in "user_register_password_second" with "<password_second>"
    And I fill in "user_register_email" with "<email>"
    And I press "Créer votre compte ToDoList"
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
