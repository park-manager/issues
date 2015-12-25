<?php

/*
 * This file is part of the Park-Manager project.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use ParkManager\Core\EntryPoints\Http\Application;
use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\Debug\Debug;

// Don't use the class caches when xdebug is enabled
// so its easier to debug errors.
$loadClassCache = !isset($_SERVER['APP_DEBUG'], $_COOKIE['XDEBUG_SESSION']);

if ($loadClassCache) {
    $loader = require_once __DIR__.'/../var/bootstrap.php.cache';
} else {
    $loader = require_once __DIR__.'/../app/autoload.php';
}

if (isset($_SERVER['APP_DEBUG'])) {
    Debug::enable();
} elseif (extension_loaded('apc')) {
    $apcLoader = new ApcClassLoader('parkmanager', $loader);
    $loader->unregister();
    $apcLoader->register(true);
}

$application = new Application($loadClassCache);
$application->run();
