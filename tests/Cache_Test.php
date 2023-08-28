<?php

class Cache_Test extends PHPUnit\Framework\TestCase {

	protected $dirname;
	protected $dir;
	protected $logger;

	protected function setUp(): void {
		global $mcServers;
		$CACHE_SERVERS = [
			'default' => ['host'=>'localhost', 'port' => 11211],
			'global' => ['host'=>'localhost', 'port' => 11212],
		];
		foreach ($CACHE_SERVERS as $server_name=>$server) {
			$mcServers[$server_name] = new \AKEB\Cache\newMemcache(true);
			if (!@$mcServers[$server_name]->connect($server['host'], $server['port'])) {
				$mcServers[$server_name] = false;
			}
		}

		$this->dirname = 'tests/tmp';
		@mkdir($this->dirname);
		$this->dir = dir($this->dirname);
	}

	protected function tearDown(): void {
		global $mcServers;
		$mcServers = [];

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
		$cache = new \Cache('testCache');
		$this->assertInstanceOf('Cache', $cache);

	}

	public function testMemcache() {
		$key = 'testMemcacheKey_'.time();
		$value = "testMemcacheValue_".time();
		$cache = new \Cache($key, 'global');
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

		$cache2 = new \Cache($key, 'default');
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

	public function testFile() {
		if (!defined('USE_FILE_CACHE')) define('USE_FILE_CACHE', true);
		if (!defined('PATH_CACHE')) define('PATH_CACHE', $this->dirname.'/');

		$key = 'testFileKey_'.time();
		$value = "testFileValue_".time();
		$cache = new \Cache($key, 'global');
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

		$cache2 = new \Cache($key, 'default');
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
