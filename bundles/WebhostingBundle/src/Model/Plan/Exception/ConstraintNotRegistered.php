<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Model\Plan\Exception;

use RuntimeException;
use function class_exists;
use function sprintf;

final class ConstraintNotRegistered extends RuntimeException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Webhosting Plan Constraint with id "%s" is not registered.', $id));
    }

    public static function withName(string $name): self
    {
        if (! class_exists($name)) {
            return new self(sprintf('Webhosting Plan Constraint %s cannot be found.', $name));
        }

        return new self(sprintf('Webhosting Plan Constraint %s is not registered.', $name));
    }
}
