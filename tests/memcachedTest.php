<?php

use AKEB\Cache\newMemcache;

class memcachedTest extends PHPUnit\Framework\TestCase {

	public function testClassExists() {
		if (extension_loaded('memcached')) {
			$this->assertTrue(class_exists('Memcached') ? true : false , 'Class Memcached not found');
		} elseif (extension_loaded('memcache')) {
			$this->assertTrue(class_exists('Memcache') ? true : false , 'Class Memcache not found');
		}
	}

	public function testMemcacheFunctions() {
		$memcache_obj1 = new newMemcache();
		$status = $memcache_obj1->connect('localhost', 11288);
		$this->assertFalse($status, 'Connect status error');

		$memcache_obj = new newMemcache();
		$status = $memcache_obj->connect('localhost', 11211);
		$this->assertTrue($status, 'Connect error');
		$rand = rand(0, mt_getrandmax());
		$memcache_obj->set('TestKey', $rand, 10);
		$cache_rand = $memcache_obj->get('TestKey');
		$this->assertEquals($rand, $cache_rand, 'set get Error');

	}

}
