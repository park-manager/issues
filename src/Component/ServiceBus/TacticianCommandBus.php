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

namespace ParkManager\Component\ServiceBus;

use League\Tactician\CommandBus as ServiceBus;
use ParkManager\Component\ApplicationFoundation\Command\CommandBus;

final class TacticianCommandBus implements CommandBus
{
    private $bus;

    public function __construct(ServiceBus $bus)
    {
        $this->bus = $bus;
    }

    public function handle(object $query)
    {
        return $this->bus->handle($query);
    }
}