<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Application\Command\DomainName;

use ParkManager\Domain\DomainName\DomainNameRepository;
use ParkManager\Domain\Webhosting\Space\WebhostingSpaceRepository;

final class AssignDomainNameToSpaceHandler
{
    private DomainNameRepository $domainNameRepository;
    private WebhostingSpaceRepository $spaceRepository;

    public function __construct(DomainNameRepository $domainNameRepository, WebhostingSpaceRepository $spaceRepository)
    {
        $this->domainNameRepository = $domainNameRepository;
        $this->spaceRepository = $spaceRepository;
    }

    public function __invoke(AssignDomainNameToSpace $command): void
    {
        $space = $this->spaceRepository->get($command->space);
        $domainName = $this->domainNameRepository->get($command->id);

        $domainName->transferToSpace($space, $command->primary);

        $this->spaceRepository->save($space);
        $this->domainNameRepository->save($domainName);
    }
}
