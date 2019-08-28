<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Domain\Account\Event;

use ParkManager\Component\DomainEvent\DomainEvent;
use ParkManager\Bundle\WebhostingBundle\Domain\Account\WebhostingAccountId;
use ParkManager\Bundle\WebhostingBundle\Domain\Package\Capabilities;

final class WebhostingAccountCapabilitiesWasChanged extends DomainEvent
{
    private $id;
    private $capabilities;

    public function __construct(WebhostingAccountId $id, Capabilities $capabilities)
    {
        $this->id           = $id;
        $this->capabilities = $capabilities;
    }

    public function id(): WebhostingAccountId
    {
        return $this->id;
    }

    public function capabilities(): Capabilities
    {
        return $this->capabilities;
    }
}
