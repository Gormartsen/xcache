<?php
  
/**
 * Defines a XCache cache implementation.
 *
 * This is XCache's cache implementation. It uses the XCache variable to store
 * cached data. Each cache bin corresponds by prefix.
 */
class XCacheCache implements BackdropCacheInterface {
  protected $bin;

  /**
   * Constructs a new BackdropDatabaseCache object.
   */
  function __construct($bin) {
    // All cache tables should be prefixed with 'cache_', except for the
    // default 'cache' bin.
    if ($bin != 'cache') {
      $bin = 'cache_' . $bin;
    }
    $this->bin = $bin;

  }

  /**
   * Implements BackdropCacheInterface::get().
   */
  function get($cid) {
    $cids = array($cid);
    $cache = $this->getMultiple($cids);
    return reset($cache);
  }

  /**
   * Implements BackdropCacheInterface::getMultiple().
   */
  function getMultiple(array &$cids) {
    try {
      $cache = array();
      foreach($cids as $cid) {
        if (xcache_isset($this->bin . ':' . $cid)) {
          $item = xcache_get($this->bin . ':' . $cid);
          if($item = $this->prepareItem($item)){
            $cache[$cid] = $item;
          }
        }
      }
      $cids = array_diff($cids, array_keys($cache));
      return $cache;
    }
    catch (Exception $e) {
      // If the xcache is not available, cache requests should
      // return FALSE in order to allow exception handling to occur.
      return array();
    }      

  }

  /**
   * Prepares a cached item.
   *
   * Checks that items are either permanent or did not expire, and unserializes
   * data as appropriate.
   *
   * @param $cache
   *   An item loaded from BackdropCacheInterface::get() or BackdropCacheInterface::getMultiple().
   *
   * @return
   *   The item with data unserialized as appropriate or FALSE if there is no
   *   valid item to load.
   */
  protected function prepareItem($cache) {
    $item = new stdClass();
    if(!$item->data = unserialize($cache)){
      return FALSE;
    }
    return $item;
  }
  
  /**
   * Implements BackdropCacheInterface::set().
   */
  function set($cid, $data, $expire = CACHE_PERMANENT) {
    $data = serialize($data);
    try {
      if($expire === CACHE_PERMANENT) {
        xcache_set($this->bin . ':' . $cid, $data);
      } else {
        xcache_set($this->bin . ':' . $cid, $data, $expire);
      }
    }
    catch (Exception $e) {
      // The XCache may not be available, so we'll ignore these calls.
    }
  }

  /**
   * Implements BackdropCacheInterface::delete().
   */
  function delete($cid) {
    xcache_unset($this->bin . ':' . $cid);
  }

  /**
 * Implements BackdropCacheInterface::deleteMultiple().
 */
  function deleteMultiple(array $cids) {
    foreach($cids as $cid) {
      xcache_unset($this->bin . ':' . $cid);
    }
  }

  /**
   * Implements BackdropCacheInterface::deletePrefix().
   */
  function deletePrefix($prefix) {
    xcache_unset_by_prefix($this->bin . ':' . $prefix);
  }

  /**
   * Implements BackdropCacheInterface::flush().
   */
  function flush() {
    xcache_unset_by_prefix($this->bin . ':' );
  }

  /**
   * Implements BackdropCacheInterface::garbageCollection().
   */
  function garbageCollection() {
    // No need to call anything. It is cleaning automatically.
  }

  /**
   * Implements BackdropCacheInterface::isEmpty().
   */
  function isEmpty() {
    return TRUE;
  }
}
