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
     */
    public UserId $id;

    /**
     * READ-ONLY.
     */
    public EmailAddress $email;

    /**
     * READ-ONLY.
     */
    public string $displayName;

    /**
     * The authentication password-hash.
     *
     * READ-ONLY.
     */
    public string $password;

    public bool $requireNewPassword = false;

    /**
     * @param string $password An encoded password string (not plain)
     */
    public function __construct(UserId $id, EmailAddress $email, string $displayName, string $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->displayName = $displayName;
        $this->password = $password;
    }

    /**
     * @param string $password An encoded password string (not plain)
     */
    public static function with(string $id, string $email, string $displayName, string $password): self
    {
        return new self(UserId::fromString($id), new EmailAddress($email), $displayName, $password);
    }

    public function requireNewPassword(): self
    {
        $this->requireNewPassword = true;

        return $this;
    }
}
