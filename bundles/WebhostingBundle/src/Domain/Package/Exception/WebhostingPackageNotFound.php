<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Domain\Package\Exception;

use InvalidArgumentException;
use ParkManager\Bundle\WebhostingBundle\Domain\Package\WebhostingPackageId;
use function sprintf;

final class WebhostingPackageNotFound extends InvalidArgumentException
{
    public static function withId(WebhostingPackageId $id): self
    {
        return new self(sprintf('Webhosting package with id "%s" does not exist.', $id->toString()));
    }
}
