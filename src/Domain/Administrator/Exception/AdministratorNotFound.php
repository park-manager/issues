<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Domain\Administrator\Exception;

use ParkManager\Domain\Administrator\AdministratorId;
use ParkManager\Domain\EmailAddress;
use ParkManager\Domain\Exception\NotFoundException;

final class AdministratorNotFound extends NotFoundException
{
    public static function withId(AdministratorId $id): self
    {
        return new self(\sprintf('Administrator with id "%s" does not exist.', $id->toString()));
    }

    public static function withEmail(EmailAddress $email): self
    {
        return new self(\sprintf('Administrator with email address "%s" does not exist.', $email->toString()));
    }
}
