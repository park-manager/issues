<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Doctrine\Package;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use InvalidArgumentException;
use ParkManager\Bundle\WebhostingBundle\Model\Package\Capabilities;
use ParkManager\Bundle\WebhostingBundle\Package\CapabilitiesFactory;
use RuntimeException;

final class WebhostingCapabilitiesType extends JsonType
{
    /** @var CapabilitiesFactory|null */
    private $capabilitiesFactory;

    public function setCapabilitiesFactory(?CapabilitiesFactory $capabilitiesFactory): void
    {
        $this->capabilitiesFactory = $capabilitiesFactory;
    }

    /**
     * @param Capabilities|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (! $value instanceof Capabilities) {
            throw new InvalidArgumentException('Expected Capabilities instance.');
        }

        return parent::convertToDatabaseValue($value->toIndexedArray(), $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Capabilities
    {
        $val = parent::convertToPHPValue($value, $platform) ?? [];

        if (! isset($this->capabilitiesFactory)) {
            throw new RuntimeException('setCapabilitiesFactory() needs to be called before this type can be used.');
        }

        return $this->capabilitiesFactory->reconstituteFromStorage($val);
    }

    public function getName(): string
    {
        return 'webhosting_capabilities';
    }
}
