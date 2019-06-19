<?php

namespace AKEB\Cache;

class newMemcache {
	private $memcache_object;
	private $memcached = false;
	private $memcache = false;

	public function __construct() {
		if (extension_loaded('memcached') && class_exists('Memcached')) {
			$this->memcache_object = new \Memcached();
			$this->memcache_object->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
			$this->memcached = true;
			$this->memcache = false;
		} elseif (extension_loaded('memcache') && class_exists('Memcache')) {
			$this->memcache_object = new \Memcache();
			$this->memcached = false;
			$this->memcache = true;
		} else {
			$this->memcached = false;
			$this->memcache = false;
			return null;
		}
	}

	public function connect($host, $port=11211) {
		if ($this->memcached) {
			if (@$this->memcache_object->addServer($host, $port)) {
				$statuses = $this->memcache_object->getStats();
				return isset($statuses[$host.":".$port]) && $statuses[$host.":".$port]["pid"] > 0;
			}
			return false;
		} elseif ($this->memcache) {
			if (@$this->memcache_object->pconnect($host, $port)) {
				$this->memcache_object->setCompressThreshold(256*1024*1024);
				return true;
			}
			return false;
		}
		return false;
	}

	public function set($key, $value, $expiration=0) {
		if ($this->memcached) {
			return @$this->memcache_object->set($key, $value, $expiration);
		} elseif ($this->memcache) {
			return @$this->memcache_object->set($key, $value, 0, $expiration);
		}
		return false;
	}

	public function add($key, $value, $expiration=0) {
		if ($this->memcached) {
			return @$this->memcache_object->add($key, $value, $expiration);
		} elseif ($this->memcache) {
			return @$this->memcache_object->add($key, $value, 0, $expiration);
		}
		return false;
	}

	public function get($key) {
		if ($this->memcached || $this->memcache) {
			return @$this->memcache_object->get($key);
		}
		return null;
	}

	public function delete($key, $timeout=0) {
		if ($this->memcached || $this->memcache) {
			return @$this->memcache_object->delete($key, $timeout);
		}
		return null;
	}

}
