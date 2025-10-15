<?php

global $globalLockId; // process-wide cache link and locker id
$globalLockId = rand();

global $mcServers;
$mcServers = array();

global $CACHE_SERVERS;
/**
 * $CACHE_SERVERS = [
 * 		'default' => ['host'=>'localhost', 'port' => 11211],
 * 		'global' => ['host'=>'localhost', 'port' => 11212],
 * ];
 *
 */
if (!$CACHE_SERVERS) $CACHE_SERVERS = [];
if (
	(extension_loaded('memcached') && class_exists('Memcached')) ||
	(extension_loaded('memcache') && class_exists('Memcache'))
) {
	foreach ($CACHE_SERVERS as $server_name=>$server) {
		$mcServers[$server_name] = new \AKEB\Cache\newMemcache();
		if (!@$mcServers[$server_name]->connect($server['host'], $server['port'])) {
			$mcServers[$server_name] = false;
		}
	}
}

class Cache {

	public static $file_cache_enable = true;
	public static $memcache_cache_enable = true;

	private $cacheObject = null;

	/**
	 * __construct
	 *
	 * @param string $cacheId
	 * @param string $server_name='default'
	 * @param boolean $force_use_file=false
	 * @return void
	 */
	function __construct($cacheId, $server_name='default', $force_use_file=false) {
		global $mcServers;
		$file_cache_enable = static::$file_cache_enable;
		$memcache_cache_enable = static::$memcache_cache_enable;

		if ($force_use_file || (defined('USE_FILE_CACHE') && constant('USE_FILE_CACHE'))) {
			$file_cache_enable = true;
			$memcache_cache_enable = false;
		}
		if (!extension_loaded('memcached') && !extension_loaded('memcache') && !class_exists('Memcached') && !class_exists('Memcache')) {
			$memcache_cache_enable = false;
		}
		if (!$mcServers) {
			$mcServers = [];
			$memcache_cache_enable = false;
		}

		if ($memcache_cache_enable) {
			$this->cacheObject = new \AKEB\Cache\Cache_Memcache($cacheId, $server_name);
		} elseif ($file_cache_enable) {
			$this->cacheObject = new \AKEB\Cache\Cache_File($cacheId, $server_name);
		}
	}

	public function &get() {
		return $this->cacheObject ? $this->cacheObject->get() : false;
	}

	public function isValid() {
		return $this->cacheObject ? $this->cacheObject->isValid() : false;
	}

	public function getTTL() {
		return $this->cacheObject ? $this->cacheObject->getTTL() : 0;
	}

	public function tryLock() {
		return $this->cacheObject ? $this->cacheObject->tryLock() : false;
	}

	public function freeLock() {
		return $this->cacheObject ? $this->cacheObject->freeLock() : true;
	}

	public function update($data, $ttl) {
		return $this->cacheObject ? $this->cacheObject->update($data, $ttl) : false;
	}

	public function remove() {
		return $this->cacheObject ? $this->cacheObject->remove() : true;
	}
}