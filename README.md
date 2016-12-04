XCache
----
`XCache` - is a fast, stable ​PHP opcode cacher that has been proven and is now running on production servers under high load.

This module provide ability to store all cached data in XCache var storage.

---
WARNING

* XCache available only for PHP 5.x version.
* To make this module works you need to have access to change php.ini file. See #INSTALATION.
* You need to have xcache extension enabled and properly configured.
* Right now module provides only Cache class to replace BackdropDatabaseCache. No need to enable module. Just download it into modules folder.

---
INSTALATION


1. Download module to modules folder.
2. Change xcache.ini or php.ini file by adding next settings:
  ```
  xcache.var_namespace_mode = 1
  xcache.var_namespace = "HTTP_HOST"
  ```
  
  This settings allow to separate cached variables by domain name.

  Also you have to make sure that next variables properly set as well:
  ```
  xcache.var_size = 100M ; memory size for variables
  ```
  
  Seee more details (here)[https://xcache.lighttpd.net/wiki/XcacheIni]
3. Change settings.php by adding next lines:
  ```
  $settings['cache_default_class'] = 'XCacheCache';
  $settings['cache_backends'] = array('modules/xcache/xcache.class.php');
  ```

---
Maintainters:

* Gor Martsen (https://github.com/Gormartsen)