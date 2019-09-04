<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Plan;

use ParkManager\Bundle\WebhostingBundle\Model\Account\WebhostingAccountId;
use ParkManager\Bundle\WebhostingBundle\Model\Plan\Constraint;

/**
 * A ConstraintValidator validates the operation doesn't violate
 * the constraint.
 *
 * For example if a webhosting account is limited to 10 mailboxes
 * the validator must check if the current amount of mailboxes (within the account)
 * does not exceed this limit.
 *
 * Caution: The account's Constraints can be updated any moment, so when
 * the account already has 10 mailboxes and the Constraint was updated
 * to only allow 8 the validator still MUST throw an ConstraintExceeded.
 */
interface ConstraintValidator
{
    /**
     * @param Constraint $constraint Constraint configuration (as assigned)
     * @param array      $context    Additional information about the operation
     *                               (implement dependent - not required)
     *
     * @throws ConstraintExceeded (instance) when a constraint is violated
     */
    public function validate(WebhostingAccountId $accountId, Constraint $constraint, array $context = []): void;
}
