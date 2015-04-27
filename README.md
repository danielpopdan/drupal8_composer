# Drupal 8 deployment with composer

Deploying Drupal with [Composer](http://getcomposer.org).
Based on [Drush Composer] (https://www.drupal.org/project/composer).
See [Drupal 8 deployment: Meet Composer] (http://slides.com/popdandaniel/drupal8_deployment_with_composer)

## Description

This package downloads Drupal Core and contrib modules. Also creates symlinks
for custom codes (modules, themes, profiles, config, etc) so you can concentrate
only on custom development, not carrying about the dependency management. In the
future it will include a Drupal configuration management, trying to resolve the
export of configurations between site instances.

## Installation

To install drush composer

    drush dl composer-8.x-1.x

The symlink_settings.yml file will create the symlinks into the ../web directory.
Each directory listed here will be symlinked to the web directory from the
composer directory. The key represents the target directory and the value will
be the link. Example:

    modules: 'modules/custom'
    profiles: 'profiles'
    themes: 'themes'
    config: 'config'

A shared directory will be created (../shared) where the files directory,
settings.php and services.yml (with the right permissions) wil be. Symlinks from
this directory to ../web/sites/default/ will be created.

At each install/update the directory structure is created. Your directory
structure should look like:

    drupal_composer
      composer (with the git repository, including this package)
      shared (the shared files)
      web (including the drupal site)

## Usage

To get the list of commands just use:

    drush composer


## Develop

To update Composer and all dependencies, run:

    curl -sS https://getcomposer.org/installer | php
    php composer.phar update


To re-build the `composer.drush.json` file, run the following:

    vendor/composer/composer/bin/composer list --format=json > composer.drush.json
