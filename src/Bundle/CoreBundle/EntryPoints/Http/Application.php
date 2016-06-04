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

namespace ParkManager\Bundle\CoreBundle\EntryPoints\Http;

use Aequasi\Environment\Environment;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the web request and response of the application.
 */
class Application
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \AppKernel
     */
    protected $kernel;

    /**
     * @var bool
     */
    protected $loadClassCache;

    /**
     * Constructor.
     *
     * @param bool $loadClassCache
     */
    public function __construct($loadClassCache = true)
    {
        $this->environment = new Environment('APP_ENV', 'app.env');
        $this->loadClassCache = $loadClassCache;

        $this->buildRequest();
        $this->buildKernel();
    }

    public function run()
    {
        $response = $this->kernel->handle($this->request);
        $response->send();

        $this->kernel->terminate($this->request, $response);
    }

    protected function buildRequest()
    {
        Request::enableHttpMethodParameterOverride();

        $this->request = Request::createFromGlobals();
    }

    protected function buildKernel()
    {
        $this->kernel = new \AppKernel(
            $this->environment->getType(), $this->environment->isDebug()
        );

        if ($this->loadClassCache) {
            $this->kernel->loadClassCache();
        }

        if (true === (bool) $this->request->server->get('APP_CACHE', false)) {
            $this->kernel = new \AppCache($this->kernel);
        }
    }
}
