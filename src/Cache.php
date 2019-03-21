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
		if (
			(defined('USE_FILE_CACHE') && USE_FILE_CACHE) ||
			(!extension_loaded('memcached') && !extension_loaded('memcache') && !class_exists('Memcached') && !class_exists('Memcache')) ||
			(!$mcServers) || $force_use_file
		) {
			$this->cacheObject = new \AKEB\Cache\Cache_File($cacheId, $server_name);
		} else {
			$this->cacheObject = new \AKEB\Cache\Cache_Memcache($cacheId, $server_name);
		}
	}

	public function &get() {
		return $this->cacheObject->get();
	}

	public function isValid() {
		return $this->cacheObject->isValid();
	}

	public function tryLock() {
		return $this->cacheObject->tryLock();
	}

	public function freeLock() {
		return $this->cacheObject->freeLock();
	}

	public function update($data, $ttl) {
		return $this->cacheObject->update($data, $ttl);
	}

	public function remove() {
		return $this->cacheObject->remove();
	}
}