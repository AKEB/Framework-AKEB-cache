<?php

namespace AKEB\Cache;

class Cache_Memcache {
	private $id = false;
	private $lId = 0;
	private $pId = 0;
	private $server_name = 'default';

	const LOCK_TTL = 120;
	const TTL_MULTIPLIER = 2;

	public function __construct($cacheId, $server_name='default') {
		global $globalLockId,$mcServers;
		if ($server_name && $mcServers[$server_name]) $this->server_name = $server_name;
		$this->cacheId($cacheId);
		$this->lId = $globalLockId;
		$this->pId = getmypid();
	}

	public function &get() {
		if (!$this->mcServer() || !$this->cacheId()) return false;
		$data = $this->mcServer()->get($this->cacheId());
		return $data;
	}

	public function isValid() {
		if (!$this->mcServer() || !$this->cacheId()) return false;
		if (defined('NO_CACHE') && constant('NO_CACHE')) return false;
		if (isset($_REQUEST['no_cache']) && $_REQUEST['no_cache']) return false;
		$ttlInfo = $this->mcServer()->get($this->cacheId().".ttl");
		if (!is_array($ttlInfo) && $ttlInfo) {
			$ttlInfo = @unserialize($ttlInfo);
		}
		return $ttlInfo !== false ? ((int)$ttlInfo["createTime"] + (int)$ttlInfo["ttl"]) > time() : false;
	}

	public function tryLock() {
		if (!$this->mcServer() || !$this->cacheId()) return false;
		if ($this->haveLock()) return true;
		$lockData = array($this->pid(), $this->lockId());
		$this->mcServer()->add($this->cacheId().".lock", $lockData, self::LOCK_TTL);
		return $this->haveLock();
	}

	public function freeLock() {
		if (!$this->mcServer()) return false;
		return $this->mcServer()->delete($this->cacheId().".lock",0);
	}

	public function update($data, $ttl) {
		if (!$this->mcServer() || ($ttl < 0)) return false;
		if (!$this->haveLock() && !$this->tryLock()) return false;
		if ($ttl == 0) $ttl = 60;
		$ttlInfo = array("createTime"=>time(), "ttl" => $ttl);
		$memcacheTTL = $ttl * self::TTL_MULTIPLIER;
		if ($memcacheTTL >= 30*86400) {
			$memcacheTTL = time()+$memcacheTTL;
		}
		$status = $this->mcServer()->set($this->cacheId(), $data, $memcacheTTL);
		if ($status) $this->mcServer()->set($this->cacheId().".ttl", $ttlInfo, $memcacheTTL);
		$this->freeLock();
		return $status;
	}

	public function remove() {
		if (!$this->mcServer() || !$this->cacheId()) return false;
		$ret = $this->mcServer()->delete($this->cacheId());
		$ret &= $this->mcServer()->delete($this->cacheId().".ttl");
		return $ret;
	}

	// =====================================================================

	private function cacheId($cacheId = false) {
		if ($cacheId) $this->id = $cacheId;
		return $this->id;
	}

	private function lockId() {
		return $this->lId;
	}

	private function pid() {
		return $this->pId;
	}

	public function mcServer($server_name = false) {
		global $mcServers;
		if ($server_name) $this->server_name = $server_name;
		return $mcServers[$this->server_name];
	}

	private function haveLock() {
		if (!$this->mcServer() || !$this->cacheId() || !$this->lockId()) return false;
		$lockData = $this->mcServer()->get($this->cacheId().".lock");
		$ret = $lockData != false ? ($lockData[0] == $this->pid()) && ($lockData[1] == $this->lockId()) : false;
		return $ret;
	}

	// private function isAvail() {
	// 	if (!$this->mcServer() || !$this->cacheId()) return false;
	// 	$ttlInfo = $this->mcServer()->get($this->cacheId().".ttl");
	// 	if ($ttlInfo) {
	// 		$data = $this->mcServer()->get($this->cacheId());
	// 	};
	// 	return ($ttlInfo !== false) && ($data !== false);
	// }

	// private function getTTL() {
	// 	if (!$this->mcServer() || !$this->cacheId()) return 0;
	// 	if (defined('NO_CACHE') && NO_CACHE) return 0;
	// 	if ($_REQUEST['no_cache']) return 0;
	// 	$ttlInfo = $this->mcServer()->get($this->cacheId().".ttl");
	// 	return $ttlInfo !== false ? ((int)$ttlInfo["createTime"] + (int)$ttlInfo["ttl"]) : 0;
	// }



}