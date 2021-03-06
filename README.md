Smalldb-REST
============

Simple implementation of REST API for Smalldb.


Requirements
------------

  - PHP 5.5+
  - libsmalldb


Installation
------------

  1. Use `composer install` to install all libraries.
  2. Create `api-v1.php` and `api-v1-diagram.php` as in examples.
  3. Create state machine definitions in `statemachine` directory -- see
     libsmalldb documentation for details.
  4. Before use in production environment change the `auth.class` option to
     something less permissive. -- The `config.json.php` contains option to
     allow everything. The default is to use `CookieAuth` class, but that
     requires some configuration.


Configuration
-------------

Configuration is loaded from three JSON files. Later overwrites the previous
(using `array_replace_recursive` function).

  - `config.json.php` in Smalldb-REST's directory to load default values.
  - `config.app.json.php` in the project's root directory to load
    application-specific options. This file should be under version control, do
    not put sensitive informations here.
  - `config.local.json.php` in the project's root directory to load
    installation-specific options. This file should NOT be under version
    control (put it into `.gitignore`), it is meant to contain sensitive
    informations.

To achieve forward compatibility of your configuration files do not use whole
copy of default config file in your application. Set only changed options
instead.


API Usage
---------

Read state of a state machine (machine ID = `"blogpost", 1`):

```
HTTP GET /api-v1.php/blogpost/1
```

Read transition info of a state machine (transition `edit`):

```
HTTP GET /api-v1.php/blogpost/1!edit
```

Invoke transition of a state machine (transition `edit`, parameters are passed
via `$_POST['args']`):

```
HTTP POST /api-v1.php/blogpost/1!edit
Content-Type: application/x-www-form-urlencoded

args[0][title]=Some%20title&args[1][text]=Lorem%20ipsum
```

List state machines of given type:

```
HTTP GET /api-v1.php/?type=blogpost
```


State diagram renderer
----------------------

The second function of the REST API is state diagram renderer. To retrieve
state diagram of the `blogpost` state machine use following HTTP request:

```
HTTP GET /api-v1-diagram.php?machine=blogpost&format=png
```

This mean you can simply put this HTML to your application:

```
<img src="api-v1-diagram.php?machine=blogpost&format=png" alt="state diagram">
```

Note: Diagrams are rendered using Graphviz and cached using APC. The `dot`
executable must be somewhere in PHP's PATH.


LICENSE
-------

Apache 2.0 - see LICENSE file


