Feature: Working with multidev environments
  In order to work collaboratively on Pantheon
  As a user
  I need to be able to create, remove, and alter multidev environments.

  @vcr site_create-env
  Scenario: Create a multidev environment
    Given I am authenticated
    And a site named "[[test_site_name]]"
    And the service level of "[[test_site_name]]" is "business"
    When I run "terminus site create-env --site=[[test_site_name]] --from-env=dev --to-env=multidev"
    Then I should get: "."
    And I should get:
    """
    Creating Multidev environment "multidev"
    """

  @vcr site_create-env_bad_name
  Scenario: Fail to create necessary environment
    Given I am authenticated
    And a site named "[[test_site_name]]"
    And the service level of "[[test_site_name]]" is "business"
    When I run "terminus site create-env --site=[[test_site_name]] --from-env=dev --to-env=dev"
    Then I should get:
    """
    Environment name "dev" is reserved by Pantheon and cannot becreated into a Multidev environment.
    """

  @vcr site_create-env_duplicate
  Scenario: Fail to create extant environment
    Given I am authenticated
    And a site named "[[test_site_name]]"
    And the service level of "[[test_site_name]]" is "business"
    When I run "terminus site create-env --site=[[test_site_name]] --from-env=dev --to-env=multidev"
    Then I should get:
    """
    The environment "multidev" already exists.
    """

  @vcr site_create-env_unauthorized
  Scenario: Fail to create environment due to service level
    Given I am authenticated
    And a site named "[[test_site_name]]"
    And the service level of "[[test_site_name]]" is "basic"
    When I run "terminus site create-env --site=[[test_site_name]] --from-env=dev --to-env=multidev"
    Then I should get:
    """
    This site does not have the authority to conduct this operation.
    """

  #@vcr site_delete-env
  #Scenario: Delete a multidev environment
    #Given I am authenticated
    #And a site named "[[test_site_name]]"
    #And the service level of "[[test_site_name]]" is "business"
    #When I run "terminus site delete-env--site=[[test_site_name]] --from-env=dev --to-env=multidev"
    #Then I should get:
      #"""
      #"""
    
  #@vcr site_delete-env_bad_name
  #Scenario: Fail to delete a required environment
    #Given I am authenticated
    #And a site named "[[test_site_name]]"
    #And the service level of "[[test_site_name]]" is "business"
    #When I run "terminus site delete-env --site=[[test_site_name]] --env=dev"
    #Then I should get:
      #"""
      #"""
    
  #@vcr site_delete-env_unauthorized
  #Scenario: Fail to delete an environment due to service level
    #Given I am authenticated
    #And a site named "[[test_site_name]]"
    #And the service level of "[[test_site_name]]" is "basic"
    #When I run "terminus site delete-env --site=[[test_site_name]] --env=dev"
    #Then I should get:
      #"""
      #"""
