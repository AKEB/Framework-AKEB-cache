<?php

date_default_timezone_set('Europe/Moscow');

global $CACHE_SERVERS;
$CACHE_SERVERS = [
	'default' => ['host'=>'localhost', 'port' => 11211],
	'global' => ['host'=>'localhost', 'port' => 11212],
];
require_once("../vendor/autoload.php");

$dateString = '';

$cache = new \Cache('testDate','default');
if (!$cache->isValid() && $cache->tryLock()) {
	$dateString = date("Y-m-d H:i:s", time());
	$cache->update($dateString,600);
	$cache->freeLock();
} else {
	$dateString = $cache->get();
}

echo $dateString.PHP_EOL;