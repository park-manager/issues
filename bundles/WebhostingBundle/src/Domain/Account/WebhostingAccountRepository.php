<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Domain\Account;

use ParkManager\Bundle\WebhostingBundle\Domain\Account\Exception\CannotRemoveActiveWebhostingAccount;
use ParkManager\Bundle\WebhostingBundle\Domain\Account\Exception\WebhostingAccountNotFound;

interface WebhostingAccountRepository
{
    /**
     * Get Account by id.
     *
     * @throws WebhostingAccountNotFound When no account was found with the id
     */
    public function get(WebhostingAccountId $id): WebhostingAccount;

    /**
     * Save the WebhostingAccount in the repository.
     *
     * This will either store a new account or update an existing one.
     */
    public function save(WebhostingAccount $account): void;

    /**
     * Remove an webhosting account registration from the repository.
     *
     * @throws CannotRemoveActiveWebhostingAccount When account is still active
     */
    public function remove(WebhostingAccount $account): void;
}
