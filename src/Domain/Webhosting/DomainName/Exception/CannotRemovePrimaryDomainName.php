<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Domain\Webhosting\DomainName\Exception;

use InvalidArgumentException;
use ParkManager\Domain\Webhosting\Account\WebhostingAccountId;
use ParkManager\Domain\Webhosting\DomainName\WebhostingDomainNameId;

final class CannotRemovePrimaryDomainName extends InvalidArgumentException
{
    public static function of(WebhostingDomainNameId $domainName, WebhostingAccountId $accountId): self
    {
        return new self(
            \sprintf(
                'Webhosting domain-name "%s" of account %s is marked as primary and cannot be removed.',
                $domainName->toString(),
                $accountId->toString()
            )
        );
    }
}
