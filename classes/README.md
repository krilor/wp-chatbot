# Classes

All classes should be kept in this directory.

* All classes should be kept in this directory, one for each file, prefixed _class-_. Each class should be wrapped by `class_exists()`
* Base classes in _base_ directory. These are base classes inherited by this plugin and add-ons
* Load classes through requiring _public.php_,_admin.php_,_base.php_. _shared.php_ is loaded by both public and admin.
 * admin - for admin area only
 * public - front-facing part of the plugin
