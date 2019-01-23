Feature: Authorization
  In order to access some resource on the application
  As a user
  I don't need to be logged in for public pages
  I need to be logged in for protected pages

  Scenario Outline: Access public pages
    When I go to "<url>"
    Then I should be on "<url>"

    Examples:
      | url           |
      | /login        |
      | /users        |
      | /users/create |
      | /users/1/edit |

  @loginAsUserNick
  Scenario Outline: Access protected pages
    When I go to "<url>"
    Then I should be on "<url>"

    Examples:
      | url           |
      | /             |
      | /tasks        |
      | /tasks/done   |
      | /tasks/create |
      | /tasks/1/edit |

  Scenario Outline: Redirect to the login page when access denied
    When I go to "<url>"
    Then I should be on "/login"

    Examples:
      | url           |
      | /             |
      | /tasks        |
      | /tasks/done   |
      | /tasks/create |
      | /tasks/1/edit |
