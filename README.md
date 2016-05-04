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
  2. Create state machine definitions in `statemachine` directory -- see libsmalldb documentation for details.


API Usage
---------

Read state of a state machine (machine ID = `"article", 1`):

```
HTTP GET /api-v1.php/article/1
```

Read transition info of a state machine (transition `edit`):

```
HTTP GET /api-v1.php/article/1!edit
```

Invoke transition of a state machine (transition `edit`, parameters are passed via `$_POST['args']`):

```
HTTP POST /api-v1.php/article/1!edit
Content-Type: application/x-www-form-urlencoded

args[0][title]=Some%20title&args[1][text]=Lorem%20ipsum
```

List state machines of given type:

```
HTTP GET /api-v1.php/?type=article
```


LICENSE
-------

Apache 2.0 - see LICENSE file


