<?php

if (extension_loaded('memcached')) {
class memcachedTest extends PHPUnit\Framework\TestCase {
	public function testMemcacheFunctions() {
		$this->assertTrue(class_exists('Memcached') ? true : false , 'Class Error');

		$memcache_obj = new Memcached;
		if (@$memcache_obj->addServer('localhost', 11211)) {
			$rand = rand(0, mt_getrandmax());
			$memcache_obj->set('TestKey', $rand, 10);
			$cache_rand = $memcache_obj->get('TestKey');
			$this->assertEquals($rand, $cache_rand, 'set get Error');
		} else {
			var_dump('Error Connect');
		}
	}

}
}