<?php

use AKEB\Cache\Cache_File;

class Cache_FileTest extends PHPUnit\Framework\TestCase {

	protected function setUp() {
		$this->dirname = 'tests/tmp';
		@mkdir($this->dirname);
		$this->dir = dir($this->dirname);
	}

	protected function tearDown() {
		$this->logger = null;
		while (false !== ($entry = $this->dir->read())) {
			if ($entry == '.' || $entry == '..') continue;
			unlink($this->dirname.'/'.$entry);
		}
		$this->dir->close();
		@rmdir($this->dirname);
		$this->dirname = null;
	}

	public function testClassExists() {
		$cache = new AKEB\Cache\Cache_File('testCache');
		$this->assertInstanceOf('AKEB\Cache\Cache_File', $cache);

	}

	public function testMemcache() {
		if (!defined('PATH_CACHE')) define('PATH_CACHE', $this->dirname.'/');

		$key = 'testMemcacheKey_'.time();
		$value = "testMemcacheValue_".time();
		$cache = new AKEB\Cache\Cache_File($key, 'global');
		$this->assertTrue($cache->tryLock());
		$cache->freeLock();
		if ($cache->tryLock()) {
			$cache->update($value, 3600);
			$cache->freeLock();
		} else {
			$this->assertTrue(false, 'Error lock cache key');
		}
		$this->assertTrue($cache->isValid());
		$this->assertEquals($cache->get(), $value);

		$cache2 = new Cache_File($key, 'default');
		if ($cache2->tryLock()) {
			$cache2->update("true", 3600);
			$cache2->freeLock();
		} else {
			$this->assertTrue(false, 'Error lock cache key');
		}
		$this->assertNotEquals($cache2->get(), $value);

		$cache->remove();
		$this->assertFalse($cache->isValid());
		$this->assertNotEquals($cache->get(), $value);
	}

}
