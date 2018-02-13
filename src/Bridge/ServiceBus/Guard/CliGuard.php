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

namespace ParkManager\Bridge\ServiceBus\Guard;

use ParkManager\Component\ServiceBus\MessageGuard\PermissionGuard;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Allows access when SAPI is cli and no Security-token was set.
 *
 * @author Sebastiaan Stok <s.stok@rollerworks.net>
 */
final class CliGuard implements PermissionGuard
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function decide(object $message): int
    {
        if ((PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') && null === $this->tokenStorage->getToken()) {
            return self::PERMISSION_ALLOW;
        }

        return self::PERMISSION_ABSTAIN;
    }
}
