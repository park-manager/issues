<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Infrastructure\Security;

use Serializable;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * The SecurityUser wraps around a User-model and keeps only
 * the information related to authentication.
 */
final class SecurityUser implements UserInterface, EquatableInterface, Serializable
{
    private string $username;
    private string $password;
    private array $roles;
    private bool $enabled;

    /**
     * @param string[] $roles
     */
    public function __construct(string $id, string $password, bool $enabled, array $roles)
    {
        \sort($roles, \SORT_STRING);

        $this->username = $id;
        $this->password = $password;
        $this->enabled = $enabled;
        $this->roles = $roles;
    }

    public function serialize(): string
    {
        return \serialize([
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'enabled' => $this->isEnabled(),
            'roles' => $this->getRoles(),
        ]);
    }

    public function unserialize($data): void
    {
        $info = \unserialize($data, ['allowed_classes' => false]);

        $this->username = $info['username'];
        $this->password = $info['password'];
        $this->enabled = $info['enabled'];
        $this->roles = $info['roles'];
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null; // No-op
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getId(): string
    {
        return $this->username;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function eraseCredentials(): void
    {
        // no-op
    }

    /**
     * @param static $user
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (! $user instanceof self) {
            return false;
        }

        // Should never mismatch, this is a safety precaution against a broken user-provider.
        if ($user->getUsername() !== $this->getUsername()) {
            return false;
        }

        if ($user->getPassword() !== $this->getPassword()) {
            return false;
        }

        if ($user->getRoles() !== $this->getRoles()) {
            return false;
        }

        return ! ($user->isEnabled() !== $this->isEnabled());
    }

    public function isAdmin(): bool
    {
        return \in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->isAdmin() && \in_array('ROLE_SUPER_ADMIN', $this->getRoles(), true);
    }
}
