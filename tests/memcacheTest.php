<?php

class memcacheTest extends PHPUnit\Framework\TestCase {
	public function testMemcacheFunctions() {
		$this->assertTrue(class_exists('Memcache') ? true : false);
		$this->assertTrue(function_exists('memcache_get') ? true : false);

		$memcache_obj = new Memcache;
		if (@$memcache_obj->connect('localhost', 11211)) {
			$rand = rand(0, mt_getrandmax());
			$memcache_obj->set('TestKey', $rand, MEMCACHE_COMPRESSED, 10);
			$cache_rand = $memcache_obj->get('TestKey');
			$this->assertEquals($rand, $cache_rand);
		} else {
			var_dump('Error Connect');
		}
	}

}