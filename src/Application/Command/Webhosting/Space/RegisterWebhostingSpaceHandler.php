<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Application\Command\Webhosting\Space;

use ParkManager\Domain\DomainName\DomainName;
use ParkManager\Domain\DomainName\DomainNameId;
use ParkManager\Domain\DomainName\DomainNameRepository;
use ParkManager\Domain\DomainName\Exception\DomainNameAlreadyInUse;
use ParkManager\Domain\DomainName\Exception\DomainNameNotFound;
use ParkManager\Domain\OwnerRepository;
use ParkManager\Domain\Webhosting\Constraint\PlanRepository;
use ParkManager\Domain\Webhosting\Space\Space;
use ParkManager\Domain\Webhosting\Space\WebhostingSpaceRepository;

final class RegisterWebhostingSpaceHandler
{
    private WebhostingSpaceRepository $spaceRepository;
    private PlanRepository $planRepository;
    private DomainNameRepository $domainNameRepository;
    private OwnerRepository $ownerRepository;

    public function __construct(WebhostingSpaceRepository $spaceRepository, PlanRepository $planRepository, DomainNameRepository $domainNameRepository, OwnerRepository $ownerRepository)
    {
        $this->spaceRepository = $spaceRepository;
        $this->planRepository = $planRepository;
        $this->domainNameRepository = $domainNameRepository;
        $this->ownerRepository = $ownerRepository;
    }

    public function __invoke(RegisterWebhostingSpace $command): void
    {
        $owner = $this->ownerRepository->get($command->owner);

        if ($command->planId !== null) {
            $space = Space::register(
                $command->id,
                $owner,
                $this->planRepository->get($command->planId)
            );
        } else {
            /** @psalm-suppress PossiblyNullOperand */
            $space = Space::registerWithCustomConstraints(
                $command->id,
                $owner,
                $command->customConstraints
            );
        }

        try {
            $currentRegistration = $this->domainNameRepository->getByName($command->domainName);

            if ($currentRegistration->space !== null) {
                throw new DomainNameAlreadyInUse($command->domainName, $currentRegistration->space->owner === $owner);
            }

            $currentRegistration->transferToSpace($space, true);
            $this->domainNameRepository->save($currentRegistration);
        } catch (DomainNameNotFound) {
            $primaryDomainName = DomainName::registerForSpace(DomainNameId::create(), $space, $command->domainName);
            $this->domainNameRepository->save($primaryDomainName);
        }

        $this->spaceRepository->save($space);
    }
}
