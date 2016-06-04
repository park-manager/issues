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

namespace ParkManager\Bundle\CoreBundle;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Park-Manager base application kernel.
 */
abstract class Kernel extends BaseKernel
{
    public function registerBundles()
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
        ];

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $rootDir = $this->getRootDir();

        $loader->load($rootDir.'/config/config_'.$this->environment.'.yml');

        if (is_file($file = $rootDir.'/config/config_'.$this->environment.'.local.yml')) {
            $loader->load($file);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        if ($this->isVagrantEnvironment()) {
            return '/dev/shm/park-manager/cache/'.$this->getEnvironment();
        }

        return parent::getCacheDir();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        if ($this->isVagrantEnvironment()) {
            return '/dev/shm/park-manager/logs';
        }

        return parent::getLogDir();
    }

    /**
     * @return bool
     */
    protected function isVagrantEnvironment()
    {
        return (getenv('HOME') === '/home/vagrant' || getenv('VAGRANT') === 'VAGRANT') && is_dir('/dev/shm');
    }
}
