<?php

date_default_timezone_set('Europe/Moscow');

@mkdir('./tmp/');

define('PATH_CACHE', './tmp/'); // Cache file directory

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