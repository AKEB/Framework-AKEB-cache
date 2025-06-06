<?php

namespace AKEB\Cache;

class newMemcache {

	private $memcache_class_name = '';
	private $memcache_object;
	private $memcached = false;
	private $memcache = false;
	private $disableErrMsg = false;

	public function __construct($disableErrMsg = false) {
		if (extension_loaded('memcached') && class_exists('Memcached')) {
			$this->memcache_class_name = '\\Memcached';
			$this->memcache_object = new $this->memcache_class_name();
			$this->memcache_object->setOption($this->memcache_class_name::OPT_LIBKETAMA_COMPATIBLE, true);
			$this->memcached = true;
			$this->memcache = false;
			$this->disableErrMsg = $disableErrMsg;
		} elseif (extension_loaded('memcache') && class_exists('Memcache')) {
			$this->memcache_class_name = '\\Memcache';
			$this->memcache_object = new $this->memcache_class_name();
			$this->memcached = false;
			$this->memcache = true;
		} else {
			$this->memcached = false;
			$this->memcache = false;
			return;
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
				$this->errorMessage(__METHOD__.':'.__LINE__, $key);
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
			if ($ret === false && $this->memcache_object->getResultCode() !== $this->memcache_class_name::RES_NOTSTORED) {
				$this->errorMessage(__METHOD__.':'.__LINE__, $key);
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
			if ($ret === false && $this->memcache_object->getResultCode() !== $this->memcache_class_name::RES_NOTFOUND) {
				$this->errorMessage(__METHOD__.':'.__LINE__, $key);
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
			if ($ret === false && $this->memcache_object->getResultCode() !== $this->memcache_class_name::RES_NOTFOUND) {
				$this->errorMessage(__METHOD__.':'.__LINE__, $key);
			}
			return $ret;
		} else if ($this->memcache) {
			return @$this->memcache_object->delete($key, $timeout);
		}
		return null;
	}


	private function errorMessage($message, $key='') {
		if ($this->disableErrMsg) return;
		$err_code = $this->memcache_object->getResultCode();
		$err_msg = $this->memcache_object->getResultMessage();
		error_log($message.' '.$key.' error: ('.$err_code.') - '.$err_msg);
	}
}
