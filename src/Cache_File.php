<?php
/**
 * You need define two constant
 *
 * PATH_CACHE
 * and
 * SERVER_ROOT
 *
 */

if ( !defined('PATH_CACHE') ) define('PATH_CACHE', SERVER_ROOT.'shared/cache/');

class Cache_File {
	var $id = false;
	var $tmpfd = false;
	var $path = '';

	function __construct($cacheId, $path='') {
		$this->path = $path ? PATH_CACHE.$path.'_': PATH_CACHE;
		$this->cacheId($cacheId);
	}

	function cacheId($cacheId=false) {
		if ($cacheId) $this->id = $cacheId;
		return $this->id;
	}

	/**
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	function &get() {
		if (!$this->cacheId()) return false;
		$data = false;
		do {	// some kind of race
			if (!file_exists($this->_fname())) break;
			$data = @file_get_contents($this->_fname());
		} while (!$data && !$i++);
		if ($data) $data = unserialize($data);
		return $data;
	}

	function isAvail() {
		if (!$this->cacheId()) return false;
		return @file_exists($this->_fname());
	}

	function isValid() {
		if (!$this->cacheId()) return false;
		if (defined('NO_CACHE') && NO_CACHE) return false;
		if (isset($_REQUEST['no_cache']) && $_REQUEST['no_cache']) return false;
		if (!file_exists($this->_fname())) return false;
		return @filemtime($this->_fname()) > time();
	}

	function getTTL() {
		if (!$this->cacheId()) return 0;
		if (defined('NO_CACHE') && NO_CACHE) return 0;
		if ($_REQUEST['no_cache']) return 0;
		if (!file_exists($this->_fname())) return 0;
		return filemtime($this->_fname());
	}

	function haveLock() {
		return !empty($this->tmpfd);
	}

	function tryLock() {
		if (!$this->cacheId()) return false;
		if ($this->haveLock()) return true;
		$this->tmpfd = @fopen($this->_fname().'.tmp',"x");
		return $this->haveLock();
	}

	function freeLock() {
		if ($this->haveLock()) @fclose($this->tmpfd);
		$this->tmpfd = false;
		if (!file_exists($this->_fname().'.tmp')) return;
		return @unlink($this->_fname().'.tmp');
	}

	function update($data, $ttl) {
		if ($ttl < 0) return false;
		if (!$this->haveLock() && !$this->tryLock()) return false;
		fwrite($this->tmpfd,serialize($data));
		fclose($this->tmpfd);
		$this->tmpfd = false;
		$fn = $this->_fname();
		$tmpfn = $this->_fname().'.tmp';
		@touch($tmpfn,time()+$ttl);
		@chmod($tmpfn,0666);
		$status = @rename($tmpfn,$fn) || (@copy($tmpfn,$fn) && @touch($fn,time()+$ttl));
		$this->freeLock();
		return $status;
	}

	function setTTL($ttl) {
		$fn = $this->_fname();
		if (!$fn) return false;
		return @touch($fn,time()+$ttl);
	}

	function remove() {
		if (!$this->cacheId()) return false;
		return @unlink($this->_fname());
	}

	// =====================================================================
	function _fname() {
		if (!$this->cacheId()) return false;
		return $this->path.'cache_'.$this->cacheId().'.dat';
	}
}