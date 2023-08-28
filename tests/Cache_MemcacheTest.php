<?php

class Cache_MemcacheTest extends PHPUnit\Framework\TestCase {

	protected function setUp() {
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
	}

	protected function tearDown() {
		global $mcServers;
		$mcServers = [];
	}

	public function testClassExists() {
		$cache = new \AKEB\Cache\Cache_Memcache('testCache');
		$this->assertInstanceOf('AKEB\Cache\Cache_Memcache', $cache);
	}

	public function testMemcache() {
		$key = 'testMemcacheKey_'.time();
		$value = "testMemcacheValue_".time();
		$cache = new \AKEB\Cache\Cache_Memcache($key, 'global');
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

		$key = 'testMemcacheKey1_'.time();
		$value = "testMemcacheValue1_".time();
		$cache = new \AKEB\Cache\Cache_Memcache($key, 'global');
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


		$cache2 = new \AKEB\Cache\Cache_Memcache($key, 'default');
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
