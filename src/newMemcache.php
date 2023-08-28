<?php

namespace AKEB\Cache;

class newMemcache {
	private $memcache_object;
	private $memcached = false;
	private $memcache = false;
	private $disableErrMsg = false;

	public function __construct($disableErrMsg = false) {
		if (extension_loaded('memcached') && class_exists('Memcached')) {
			$this->memcache_object = new \Memcached();
			$this->memcache_object->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
			$this->memcached = true;
			$this->memcache = false;
			$this->disableErrMsg = $disableErrMsg;
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
			$servers = @$this->memcache_object->getServerList();
			foreach ($servers as $server) {
				if ($server['host'] == $host && $server['port'] == $port) {
					return true;
				}
			}
			return @$this->memcache_object->addServer($host, $port);
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
			$ret = @$this->memcache_object->set($key, $value, $expiration);
			if ($ret === false) {
				$this->errorMessage(__METHOD__);
			}
			return $ret;
		} elseif ($this->memcache) {
			return @$this->memcache_object->set($key, $value, 0, $expiration);
		}
		return false;
	}

	public function add($key, $value, $expiration=0) {
		if ($this->memcached) {
			$ret = @$this->memcache_object->add($key, $value, $expiration);
			if ($ret === false && $ret !== \Memcached::RES_NOTSTORED) {
				$this->errorMessage(__METHOD__);
			}
			return $ret;
		} elseif ($this->memcache) {
			return @$this->memcache_object->add($key, $value, 0, $expiration);
		}
		return false;
	}

	public function get($key) {
		if ($this->memcached) {
			$ret = @$this->memcache_object->get($key);
			if ($ret === false && $ret !== \Memcached::RES_NOTFOUND) {
				$this->errorMessage(__METHOD__);
			}
			return $ret;
		} else if ($this->memcache) {
			return @$this->memcache_object->get($key);
		}
		return null;
	}

	public function delete($key, $timeout=0) {
		if ($this->memcached) {
			$ret = @$this->memcache_object->delete($key, $timeout);
			if ($ret === false && $ret !== \Memcached::RES_NOTFOUND) {
				$this->errorMessage(__METHOD__);
			}
			return $ret;
		} else if ($this->memcache) {
			return @$this->memcache_object->delete($key, $timeout);
		}
		return null;
	}

	private function errorMessage($message) {
		if ($this->disableErrMsg) return;
		$err_code = $this->memcache_object->getResultCode();
		$err_msg = $this->memcache_object->getResultMessage();
		error_log($message.' error: ('.$err_code.') - '.$err_msg);
	}
}
