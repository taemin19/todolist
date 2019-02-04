Feature: Authorization
  In order to access some resource on the application
  As a user
  I need to be logged in
  I need to have a granted access

  Scenario Outline: Access public pages as anonymous user
    When I go to "<url>"
    Then the response status code should be 200
    And I should be on "<url>"

    Examples:
      | url           |
      | /login        |

  @loginAsUserNick
  Scenario Outline: Access protected pages as user
    Given the following tasks exist for current user:
      | title    | content            | isDone |
      | Thor     | Must defeat Loki   | 0      |
    When I go to "<url>"
    Then the response status code should be 200
    And I should be on "<url>"

    Examples:
      | url           |
      | /             |
      | /tasks/       |
      | /tasks/done   |
      | /tasks/create |
      | /tasks/1/edit |

  @loginAsAdminShield
  Scenario Outline: Access protected pages as admin
    Given the following tasks exist for current user:
      | title    | content            | isDone |
      | Thor     | Must defeat Loki   | 0      |
    Given the following users exist:
      | username | password | email          |
      | nick     | shield   | nick@fury.com  |
    When I go to "<url>"
    Then the response status code should be 200
    And I should be on "<url>"

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

  Scenario Outline: Redirect to the login page when access denied as anonymous user
    Given the following tasks exist for user Tony:
      | title    | content            | isDone |
      | Thor     | Must defeat Loki   | 0      |
    When I go to "<url>"
    Then the response status code should be 200
    And I should be on "/login"

    Examples:
      | url                 |
      | /tasks/             |
      | /tasks/done         |
      | /tasks/create       |
      | /tasks/1/edit       |
      | /tasks/1/delete     |
      | /admin/users        |
      | /admin/users/create |
      | /admin/users/1/edit |

  @loginAsUserNick
  Scenario Outline: Throw access denied as user
    Given the following tasks exist for user Tony:
      | title    | content            | isDone |
      | Thor     | Must defeat Loki   | 0      |
    When I go to "<url>"
    Then the response status code should be 403
    And I should be on "<url>"

    Examples:
      | url                 |
      | /tasks/1/edit       |
      | /tasks/1/delete     |
      | /admin/users        |
      | /admin/users/create |
      | /admin/users/1/edit |
