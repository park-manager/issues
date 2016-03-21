<?php

/*
 * This file is part of the Park-Manager project.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use ParkManager\Bundle\CoreBundle\Kernel;

/**
 * Default AppKernel.
 */
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // Put here your own bundles!
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return array_merge(parent::registerBundles(), $bundles);
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        if ($this->isVagrantEnvironment()) {
            return '/dev/shm/park-manager/cache/'.$this->getEnvironment();
        }

        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        if ($this->isVagrantEnvironment()) {
            return '/dev/shm/park-manager/logs';
        }

        return dirname(__DIR__).'/var/logs';
    }
}
