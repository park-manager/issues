<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Model\Account\Exception;

use InvalidArgumentException;
use ParkManager\Bundle\WebhostingBundle\Model\Account\WebhostingAccountId;

final class CannotRemoveActiveWebhostingAccount extends InvalidArgumentException
{
    public static function withId(WebhostingAccountId $id): self
    {
        return new self(
            \sprintf(
                'Webhosting account %s cannot be removed as it\'s still active.' .
                ' Call markForRemoval() on the WebhostingAccount instance first.',
                $id->toString()
            )
        );
    }
}
