<?php

use AKEB\Cache\newMemcache;

class newMemcacheTest extends PHPUnit\Framework\TestCase {

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
		$this->assertTrue($status, 'Connect status error');

		$memcache_obj = new newMemcache();
		$status = $memcache_obj->connect('localhost', 11211);
		$this->assertTrue($status, 'Connect error');
		$rand = rand(0, mt_getrandmax());
		$memcache_obj->set('TestKey', $rand, 10);
		$this->assertEquals($memcache_obj->get('TestKey'), $rand, 'set get Error');

		$memcache_obj->set('TestKey1', $rand, 20);
		$memcache_obj->delete('TestKey1');
		$this->assertNotEquals($memcache_obj->get('TestKey1'), $rand, 'set get Error');

		$memcache_obj->add('TestKey2', "test", 20);
		$memcache_obj->add('TestKey2', "test2", 20);
		$this->assertEquals($memcache_obj->get('TestKey2'), "test", 'set get Error');

	}

}
