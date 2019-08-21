<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Module\WebhostingModule\Infrastructure\Doctrine\DomainName;

use ParkManager\Module\CoreModule\Doctrine\DomainIdType;
use ParkManager\Module\WebhostingModule\Domain\DomainName\WebhostingDomainNameId;

final class WebhostingDomainNameIdType extends DomainIdType
{
    public const NAME         = 'park_manager_webhosting_domain_name_id';
    public const OBJECT_CLASS = WebhostingDomainNameId::class;
}
