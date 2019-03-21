# Cache

composer project akeb/cache

Composer config
```
{
	"require": {
		"akeb/cache": "^1.0.0"
	},
	"repositories": [
		{
			"type": "vcs",
			"url": "https://git.terrhq.ru/v.babajanyan/cache"
		}
	]
}
```

or

```
{
	"require": {
		"akeb/cache": "^1.0.0"
	},
	"repositories": [
		{
			"type": "vcs",
			"url": "https://github.com/AKEB/cache"
		}
	]
}
```

## If you use memcached

```
global $CACHE_SERVERS;
$CACHE_SERVERS = [
    'default' => ['host'=>'localhost', 'port' => 11211]
];

require_once("../vendor/autoload.php");
```

## If you use FileCache

```
define('USE_FILE_CACHE', true); // Forcible use of file cache

define('PATH_CACHE', '/opt/www/cache/'); // Cache file directory

require_once("../vendor/autoload.php");
```

## Example use Cache

### Memcached
```
<?php
global $CACHE_SERVERS;
$CACHE_SERVERS = [
    'default' => ['host'=>'localhost', 'port' => 11211], // Description memcached servers
];
require_once("../vendor/autoload.php");

$dateString = '';

$cache = new \Cache('testDate','default'); // Init Cache Object with name "testDate"

if (!$cache->isValid() && $cache->tryLock()) { // If cache not valid and we can lock cache
    // Do something
    $dateString = date("Y-m-d H:i:s", time());
    
    $cache->update($dateString,600); // Update cache data
    $cache->freeLock(); // Free lock
} else { // Cache valid, or we can't lock cache 
    $dateString = $cache->get(); // get data from cache
}

echo $dateString.PHP_EOL;
?>
```

### File
```
<?php
define('PATH_CACHE', './tmp/'); // Cache file directory

require_once("../vendor/autoload.php");

$dateString = '';

$cache = new \Cache('testDate','default'); // Init Cache Object with name "testDate"

if (!$cache->isValid() && $cache->tryLock()) { // If cache not valid and we can lock cache
    // Do something
    $dateString = date("Y-m-d H:i:s", time());
    
    $cache->update($dateString,600); // Update cache data
    $cache->freeLock(); // Free lock
} else { // Cache valid, or we can't lock cache 
    $dateString = $cache->get(); // get data from cache
}

echo $dateString.PHP_EOL;
?>
```