<?php

/*
 * This file is part of the ParkManager project.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ParkManager\Core\EntryPoints\Http;

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
