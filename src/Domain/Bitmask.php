<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Domain;

use InvalidArgumentException;

/**
 * Immutable Bitmask ValueObject.
 *
 * Possible masks are expected to be powers of 2 (with duplicates):
 *
 * const VIEW = 1 << 0;          // 1
 * const CREATE = 1 << 1;        // 2
 * const EDIT = 1 << 2;          // 4
 * const DELETE = 1 << 3;        // 8
 * const UNDELETE = 1 << 4;      // 16
 * const OPERATOR = 1 << 5;      // 32
 * const MASTER = 1 << 6;        // 64
 * const OWNER = 1 << 7;         // 128
 * const IDDQD = 1 << 30;        // ...
 * const ALL = self::VIEW | ... | self::EDIT;
 *
 * Originally inspired on the Symfony ACL Permission MaskBuilder.
 */
abstract class Bitmask
{
    protected int $mask;

    public function __construct(string | int ...$mask)
    {
        $this->mask = 0;

        foreach ($mask as $nibble) {
            $this->mask |= $this->resolveMask($nibble);
        }
    }

    public function get(): int
    {
        return $this->mask;
    }

    public function add(string | int ...$mask): static
    {
        $new = clone $this;

        foreach ($mask as $nibble) {
            $new->mask |= $new->resolveMask($nibble);
        }

        return $new;
    }

    public function has(string | int $flag): bool
    {
        $flag = $this->resolveMask($flag);

        return ($this->mask & $flag) === $flag;
    }

    /**
     * Returns the mask for the passed code.
     *
     * @throws InvalidArgumentException
     */
    public function resolveMask(string | int $code): int
    {
        if (\is_int($code)) {
            return $code;
        }

        $name = \sprintf('static::%s', \mb_strtoupper($code));

        if (! \defined($name)) {
            throw new InvalidArgumentException(\sprintf('The code "%s" is not supported', $code));
        }

        return \constant($name);
    }

    public function remove(string | int ...$mask): static
    {
        $new = clone $this;

        foreach ($mask as $nibble) {
            $new->mask &= ~$this->resolveMask($nibble);
        }

        return $new;
    }

    public function clear(): static
    {
        return new static(0);
    }
}
