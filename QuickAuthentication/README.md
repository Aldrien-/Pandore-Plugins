# QuickAuthentication

## Overview

The quick authenticate plugin defines a quick way to authenticate users in accordance with the Authentication plugin. It is particularly useful in development environment.

## How to install

Put `QuickAuthentication` in your `Project/Plugins` folder and add the following instruction into your Pandore configuration file (found in `Project/Config/`) under `[Plugins]`.
    
    plugins[] = QuickAuthentication

## How it works

It sets the current user group to `Admin` in session if the application is run in debug mode (see `debug` in your Pandore configuration file). Otherwise, it set the current user group to `Guest`.

## How to use

You have nothing to do.

## License

Copyright 2011-2013 [Alexandre Lemire](https://github.com/Aldrien-) & [Yannick Cladière](https://github.com/Yannz)

Licensed under the MIT license.