Feature: Navigation
  In order to navigate between the pages on the application
  As a user
  I need to be able to see some links and click on them

  Scenario Outline: Navigation bar links
    Given I am on homepage
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link           | url       |
      | To Do List app | /         |
      | Connexion      | /login    |
      | S'inscrire     | /register |

  @loginAsUserNick
  Scenario Outline: Navigation bar links
    Given I am on homepage
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link                       | url           |
      | Créer une tâche            | /tasks/create |
      | Liste des tâches à faire   | /tasks/       |
      | Liste des tâches terminées | /tasks/done   |
      | Se déconnecter             | /             |

  @loginAsAdminShield
  Scenario Outline: Navigation bar links
    Given I am on homepage
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link                   | url                 |
      | Créer un utilisateur   | /admin/users/create |
      | Liste des utilisateurs | /admin/users        |

  Scenario Outline: Login page links
    Given I am on "/login"
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link           | url       |
      | To Do List app | /         |
      | Retour         | /         |
      | S'inscrire     | /register |

  Scenario Outline: Register page links
    Given I am on "/register"
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link           | url    |
      | To Do List app | /      |
      | Retour         | /      |
      | Se connecter   | /login |

  Scenario Outline: Home page links
    Given I am on homepage
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link                           | url       |
      | Inscrivez-vous, c'est gratuit. | /register |
      | Se connecter                   | /login    |

  @loginAsUserNick
  Scenario Outline: Home page links
    Given I am on homepage
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link                                    | url           |
      | Créer une nouvelle tâche                | /tasks/create |
      | Consulter la liste des tâches à faire   | /tasks/       |
      | Consulter la liste des tâches terminées | /tasks/done   |

  @loginAsUserNick
  Scenario Outline: Tasks create page links
    Given I am on "/tasks/create"
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link                                    | url           |
      | Consulter la liste des tâches à faire   | /tasks/       |
      | Consulter la liste des tâches terminées | /tasks/done   |

  @loginAsUserNick
  Scenario Outline: Tasks list page links
    Given I am on "/tasks/"
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link                                    | url           |
      | Créer une nouvelle tâche                | /tasks/create |
      | Consulter la liste des tâches terminées | /tasks/done   |

  @loginAsUserNick
  Scenario Outline: Tasks done list page links
    Given I am on "/tasks/done"
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link                                    | url           |
      | Créer une nouvelle tâche                | /tasks/create |
      | Consulter la liste des tâches à faire   | /tasks/       |

  @loginAsUserNick
  Scenario Outline: Tasks edit page links
    Given the following tasks exist for current user:
      | title    | content            | isDone |
      | Thor     | Must defeat Loki   | 0      |
    And I am on "/tasks/1/edit"
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link                                    | url           |
      | Créer une nouvelle tâche                | /tasks/create |
      | Consulter la liste des tâches à faire   | /tasks/       |
      | Consulter la liste des tâches terminées | /tasks/done   |

  @loginAsAdminShield
  Scenario Outline: Users create page links
    Given I am on "/admin/users/create"
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link                                | url          |
      | Consulter la liste des utilisateurs | /admin/users |

  @loginAsAdminShield
  Scenario Outline: Users list page links
    Given I am on "/admin/users"
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link                         | url                 |
      | Créer un nouveau utilisateur | /admin/users/create |

  @loginAsAdminShield
  Scenario Outline: Users edit page links
    Given the following tasks exist for current user:
      | title    | content            | isDone |
      | Thor     | Must defeat Loki   | 0      |
    And I am on "/admin/users/1/edit"
    When I click "<link>"
    Then I should be on "<url>"

    Examples:
      | link                                | url                 |
      | Créer un nouveau utilisateur        | /admin/users/create |
      | Consulter la liste des utilisateurs | /admin/users        |
