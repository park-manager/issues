<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Tests\Infrastructure\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use ParkManager\Bundle\WebhostingBundle\Infrastructure\DependencyInjection\Compiler\CapabilitiesFactoryPass;
use ParkManager\Bundle\WebhostingBundle\Infrastructure\Service\Package\CapabilitiesFactory;
use ParkManager\Bundle\WebhostingBundle\Tests\Fixtures\Domain\PackageCapability\MonthlyTrafficQuota;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class CapabilitiesFactoryPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_compiles_with_no_CapabilitiesFactory_registered(): void
    {
        $this->container->setParameter(
            'park_manager.webhosting.package_capabilities',
            ['MonthlyTrafficQuota' => MonthlyTrafficQuota::class]
        );

        $this->compile();

        $this->assertContainerBuilderNotHasService(CapabilitiesFactory::class);
    }

    /** @test */
    public function it_sets_CapabilitiesFactory_mapping(): void
    {
        $this->container->setParameter(
            'park_manager.webhosting.package_capabilities',
            ['MonthlyTrafficQuota' => MonthlyTrafficQuota::class]
        );
        $this->container->register(CapabilitiesFactory::class);
        $this->compile();

        $this->assertContainerBuilderHasService(CapabilitiesFactory::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            CapabilitiesFactory::class,
            0,
            [MonthlyTrafficQuota::id() => MonthlyTrafficQuota::class]
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CapabilitiesFactoryPass());
    }
}
