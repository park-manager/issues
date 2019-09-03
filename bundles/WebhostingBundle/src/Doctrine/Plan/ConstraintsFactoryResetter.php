<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Doctrine\Plan;

use Doctrine\DBAL\Types\Type;

final class ConstraintsFactoryResetter
{
    public function reset(): void
    {
        /** @var WebhostingPlanConstraintsType $type */
        $type = Type::getType('webhosting_plan_constraints');
        $type->setConstraintsFactory(null);
    }
}