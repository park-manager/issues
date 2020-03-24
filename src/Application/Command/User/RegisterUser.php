<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Application\Command\User;

use ParkManager\Domain\EmailAddress;
use ParkManager\Domain\User\UserId;

final class RegisterUser
{
    /**
     * READ-ONLY.
     *
     * @var UserId
     */
    public $id;

    /**
     * READ-ONLY.
     *
     * @var EmailAddress
     */
    public $primaryEmail;

    /**
     * READ-ONLY.
     *
     * @var string
     */
    public $displayName;

    /**
     * The authentication password-hash.
     *
     * READ-ONLY.
     *
     * @var string|null
     */
    public $password;

    public function __construct(UserId $id, EmailAddress $primaryEmail, string $displayName, ?string $password = null)
    {
        $this->id = $id;
        $this->primaryEmail = $primaryEmail;
        $this->displayName = $displayName;
        $this->password = $password;
    }

    public static function with(string $id, string $email, string $displayName, ?string $password = null): self
    {
        return new static(UserId::fromString($id), new EmailAddress($email), $displayName, $password);
    }
}
