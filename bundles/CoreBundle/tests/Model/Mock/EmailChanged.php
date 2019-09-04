<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\CoreBundle\Tests\Model\Mock;

final class EmailChanged
{
    /** @var MockIdentity */
    private $id;

    /** @var string */
    private $email;

    public function __construct(MockIdentity $id, string $email)
    {
        $this->email = $email;
        $this->id = $id;
    }

    public function getId(): MockIdentity
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
