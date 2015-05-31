<?php

/*
 * This file is part of the ParkManager project.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use ParkManager\Core\Kernel;

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

    public function getCacheDir()
    {
        if ($this->isVagrantEnvironment()) {
            return '/dev/shm/parkmanager/cache/'.$this->environment;
        }

        return __DIR__.'/../var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        if ($this->isVagrantEnvironment()) {
            return '/dev/shm/parkmanager/logs';
        }

        return __DIR__.'/../var/logs';
    }
}
