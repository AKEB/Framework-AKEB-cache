# Cache

## Tests

[![pipeline status](https://gitlab.pvt/gapa/mrgs/cache/badges/master/pipeline.svg)](https://gitlab.pvt/gapa/mrgs/cache/-/commits/master)
[![coverage report](https://gitlab.pvt/gapa/mrgs/cache/badges/master/coverage.svg)](https://gitlab.pvt/gapa/mrgs/cache/-/commits/master)

## Install

composer project akeb/cache

Composer config

```json
{
    "require": {
        "akeb/cache": "^1.0.5"
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
<?php
global $CACHE_SERVERS;
$CACHE_SERVERS = [
    'default' => ['host'=>'localhost', 'port' => 11211]
];

require_once("../vendor/autoload.php");
?>
```

## If you use FileCache

```
<?php
define('USE_FILE_CACHE', true); // Forcible use of file cache

define('PATH_CACHE', '/opt/www/cache/'); // Cache file directory

require_once("../vendor/autoload.php");
?>
```

## Example use Cache

### Memcached cache
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

### File cache
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

See the `examples` folder for more details.

