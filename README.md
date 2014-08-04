AMP functionality
============

This is a plugin to help me structure projects and stop me from rebuilding the same core elements for every project.  The aim of this project is to extend some of the core laravel functionality, and generate boilerplate to jumpstart projects.

#### Composer Install

    "require": {
        "agandra/amp": "dev-master"
    }

#### Import config setting and run migrations

	php artisan config:publish agandra/amp

	php artisan migrate --package=agandra/amp

### AMP Philosophy

This package should be primarily used for large packages where the standard Laravel structure does not provide enough organization.  Projects will be broken down into 3 main components: Models, Services, and Repos.  Each component serves a unique purpose.

## Models

These are the standard model files that inherit from \Eloquent.  Each instance of a model represents one row from the table.  We do not use these model files to build queries to get results, in controllers.  We do not create instances of the Models in the controller files, we should only be manipulating these model files in Services and Repos.

## Repos



## Services


### AMP Validator

### AMP Reply

### AMP Model