<?php

/*
 * This file is part of the Park-Manager project.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * @var ClassLoader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
