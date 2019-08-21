<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Module\WebhostingModule\Domain\Account\Event;

use ParkManager\Bundle\CoreBundle\Domain\Shared\OwnerId;
use ParkManager\Component\DomainEvent\DomainEvent;
use ParkManager\Module\WebhostingModule\Domain\Account\WebhostingAccountId;

final class WebhostingAccountWasRegistered extends DomainEvent
{
    private $accountId;
    private $owner;

    public function __construct(WebhostingAccountId $id, OwnerId $owner)
    {
        $this->accountId = $id;
        $this->owner     = $owner;
    }

    public function id(): WebhostingAccountId
    {
        return $this->accountId;
    }

    public function owner(): OwnerId
    {
        return $this->owner;
    }
}
