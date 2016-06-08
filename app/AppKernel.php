<?php

/*
 * Copyright (c) the Contributors as noted in the AUTHORS file.
 *
 * This file is part of the Park-Manager project.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
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
