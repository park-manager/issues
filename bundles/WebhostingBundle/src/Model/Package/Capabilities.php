<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Model\Package;

use ArrayIterator;
use IteratorAggregate;
use ParkManager\Bundle\WebhostingBundle\Model\Package\Exception\CapabilityNotInSet;
use Traversable;
use function array_merge;
use function array_values;
use function get_class;

/**
 * Capabilities holds an immutable set of unique Capability objects.
 */
final class Capabilities implements IteratorAggregate
{
    /** @var Capability[] */
    private $capabilities = [];

    /** @var array[] */
    private $capabilitiesIndexed = [];

    public function __construct(Capability ...$capabilities)
    {
        foreach ($capabilities as $capability) {
            $class                                        = get_class($capability);
            $this->capabilities[$class]                   = $capability;
            $this->capabilitiesIndexed[$capability::id()] = $capability->configuration();
        }
    }

    public function add(Capability ...$capabilities): self
    {
        // Cannot unpack array with string keys (class uniqueness is guarded by the constructor)
        return new self(...array_merge(array_values($this->capabilities), $capabilities));
    }

    public function remove(Capability ...$capabilities): self
    {
        $capabilitiesList = $this->capabilities;

        foreach ($capabilities as $capability) {
            unset($capabilitiesList[get_class($capability)]);
        }

        return new self(...array_values($capabilitiesList));
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->capabilities);
    }

    public function has(string $capability): bool
    {
        return isset($this->capabilities[$capability]);
    }

    public function get(string $capability): Capability
    {
        if (! isset($this->capabilities[$capability])) {
            throw CapabilityNotInSet::withName($capability);
        }

        return $this->capabilities[$capability];
    }

    public function toIndexedArray(): array
    {
        return $this->capabilitiesIndexed;
    }

    public function equals(self $other): bool
    {
        if ($this === $other) {
            return true;
        }

        // Leave values of the array are expected to be scalar. So strict comparison is possible.
        return $this->capabilitiesIndexed === $other->capabilitiesIndexed;
    }
}
