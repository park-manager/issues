<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Application\Command\Webhosting\Space;

use ParkManager\Domain\Webhosting\Space\SpaceId;

/**
 * Mark the Webhosting Space for removal.
 *
 * Unlike ExpireSpaceOn this operation cannot be undone!
 */
final class MarkSpaceForRemoval
{
    public function __construct(public SpaceId $id)
    {
    }
}
