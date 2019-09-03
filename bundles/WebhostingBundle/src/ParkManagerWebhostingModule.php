<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\DBAL\Types\Type;
use ParkManager\Bundle\WebhostingBundle\DependencyInjection\Compiler\PlanConstraintsPass;
use ParkManager\Bundle\WebhostingBundle\DependencyInjection\DependencyExtension;
use ParkManager\Bundle\WebhostingBundle\Doctrine\Plan\WebhostingPlanConstraintsType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use function dirname;

final class ParkManagerWebhostingModule extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new DependencyExtension();
        }

        return $this->extension;
    }

    public function build(ContainerBuilder $container): void
    {
        $path = $this->getPath() . '/src/Model/';
        $container->addCompilerPass(DoctrineOrmMappingsPass::createAnnotationMappingDriver([$path => $this->getNamespace() . '\\Model'], [$path]));
        $container->addCompilerPass(new PlanConstraintsPass());
    }

    public function shutdown(): void
    {
        if (Type::hasType('webhosting_plan_constraints')) {
            /** @var WebhostingPlanConstraintsType $type */
            $type = Type::getType('webhosting_plan_constraints');
            $type->setConstraintsFactory(null);
        }
    }
}