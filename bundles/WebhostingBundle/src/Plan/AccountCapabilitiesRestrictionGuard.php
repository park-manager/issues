<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Plan;

use ParkManager\Bundle\WebhostingBundle\Model\Account\WebhostingAccountRepository;
use ParkManager\Bundle\WebhostingBundle\UseCase\AccountIdAwareCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface as PropertyAccessor;
use function get_class;

/**
 * The AccountCapabilitiesRestrictionGuard determines if the a Command violates
 * (or will violate) the WebhostingAccount Capabilities restrictions.
 *
 * Unsupported Commands or unregistered Capabilities are ignored.
 */
final class AccountCapabilitiesRestrictionGuard implements CapabilitiesRestrictionGuard
{
    private $accountRepository;
    private $capabilityGuards;
    private $propertyAccessor;
    private $mappings;

    public function __construct(WebhostingAccountRepository $accountRepository, ContainerInterface $capabilityGuards, PropertyAccessor $propertyAccessor, array $mappings)
    {
        $this->accountRepository = $accountRepository;
        $this->capabilityGuards  = $capabilityGuards;
        $this->mappings          = $mappings;
        $this->propertyAccessor  = $propertyAccessor;
    }

    public function decide(object $command): bool
    {
        $name = get_class($command);

        if (! $command instanceof AccountIdAwareCommand || ! isset($this->mappings[$name])) {
            return true;
        }

        $account        = $this->accountRepository->get($command->account());
        $capabilities   = $account->capabilities();
        $capabilityName = $this->mappings[$name]['capability'];

        if (! $capabilities->has($capabilityName)) {
            return true;
        }

        /** @var CapabilityGuard $guard */
        $guard = $this->capabilityGuards->get($capabilityName);

        return $guard->decide(
            $capabilities->get($capabilityName),
            $this->getContextFromCommand($command),
            $account
        );
    }

    private function getContextFromCommand(object $command): array
    {
        $name     = get_class($command);
        $mappings = $this->mappings[$name]['mapping'] ?? [];

        $context = [];

        foreach ($mappings as $argumentName => $propertyPath) {
            $context[$argumentName] = $this->propertyAccessor->getValue($command, $propertyPath);
        }

        return $context;
    }
}
