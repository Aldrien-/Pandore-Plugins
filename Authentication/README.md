# Authentication

## Overview

The authentication plugin manages access to each action of each module.

## How to install

Put `Authentication` in your `Project/Plugins` folder and add the following instruction into your Pandore configuration file (found in `Project/Config/`) under `[Plugins]`.
    
    plugins[] = Authentication

## How it works

It sets the current user group in the part of the session dedicated to the `AuthenticationPlugin` using the `Group` variable.

## How to use

Authentication is based on rules defined into the `Config.yaml` file. Typically, the config file looks like :

    # Whether the plugin is enabled.
    IsEnabled: true
    # Define the name of the default group.
    DefaultGroup: Guest

    Modules:
      # Permissions for the Foo Module.
      FooModule:
        actions:
          # Permissions for the default action.
          default:
            authorized: [Member]
          # Permissions for the foo action.
          fooAction:
            unauthorized: [Guest, Banned]
        # Default permissions for the Foo Module.
        permissions:
          authorized: [Member]
          unauthorized: [Banned]

In the first part, you can decide whether the plugin is enabled and you can define the name of the default group of users.

In the second part, you can define permissions for each action of each module. These permissions can be defined in two ways using an array of authorized groups or an array of unauthorized groups. You don't have to define all rules of each action, it's possible to define some global rules for a whole module. Also, it's important to note that a missing rules for an action or a module is equivalent to an authorization for all groups.

## License

Copyright 2011-2013 [Alexandre Lemire](https://github.com/Aldrien-) & [Yannick Cladière](https://github.com/Yannz)

Licensed under the MIT license.