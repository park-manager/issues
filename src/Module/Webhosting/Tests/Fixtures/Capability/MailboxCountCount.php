<?php

declare(strict_types=1);

/*
 * Copyright (c) the Contributors as noted in the AUTHORS file.
 *
 * This file is part of the Park-Manager project.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Module\Webhosting\Tests\Fixtures\Capability;

use ParkManager\Module\Webhosting\Model\Package\CommandSubscribingCapability;
use ParkManager\Module\Webhosting\Tests\Fixtures\Model\Mailbox\CreateMailbox;

final class MailboxCountCount implements CommandSubscribingCapability
{
    private $limit;

    public static function id(): string
    {
        return 'b9ea5838-97c7-11e7-a1a1-acbc32b58315';
    }

    public function __construct(string $limit)
    {
        $this->limit = $limit;
    }

    public function configuration(): array
    {
        return ['limit' => $this->limit];
    }

    public static function reconstituteFromArray(array $from): self
    {
        return new self($from['limit']);
    }

    public static function subscribedCommands(): array
    {
        return [CreateMailbox::class];
    }
}