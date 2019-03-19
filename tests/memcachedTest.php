<?php

if (extension_loaded('memcached')) {
class memcachedTest extends PHPUnit\Framework\TestCase {
	public function testMemcacheFunctions() {
		var_export(get_loaded_extensions());

		$this->assertTrue(class_exists('Memcached') ? true : false , 'Class Error');
		$this->assertTrue(function_exists('memcache_get') ? true : false, 'Function Error');

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