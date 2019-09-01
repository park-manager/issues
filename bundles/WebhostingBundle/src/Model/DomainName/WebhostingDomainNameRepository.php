<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Model\DomainName;

use ParkManager\Bundle\WebhostingBundle\Model\Account\Exception\WebhostingAccountNotFound;
use ParkManager\Bundle\WebhostingBundle\Model\Account\WebhostingAccountId;
use ParkManager\Bundle\WebhostingBundle\Model\DomainName;
use ParkManager\Bundle\WebhostingBundle\Model\DomainName\Exception\WebhostingDomainNameNotFound;

interface WebhostingDomainNameRepository
{
    /**
     * @throws WebhostingDomainNameNotFound When no domain-name was found with the id
     */
    public function get(WebhostingDomainNameId $id): WebhostingDomainName;

    /**
     * Get the primary DomainName of an webhosting account.
     *
     * @throws WebhostingAccountNotFound When no account was found with the id
     */
    public function getPrimaryOf(WebhostingAccountId $id): WebhostingDomainName;

    /**
     * Finds a WebhostingDomainName registration by it's full name.
     */
    public function findByFullName(DomainName $name): ?WebhostingDomainName;

    /**
     * Save the WebhostingDomainName in the repository.
     *
     * This will either store a new account or update an existing one.
     *
     * Note: Only one DomainName _per webhosting account_ can be marked primary,
     * any previously assigned primary domain name must have the primary
     * marking removed.
     */
    public function save(WebhostingDomainName $domainName): void;

    /**
     * Remove an webhosting domain-name registration from the repository.
     *
     * An WebhostingDomainName that's marked as primary cannot
     * be deleted (unless the account is marked for removal).
     */
    public function remove(WebhostingDomainName $domainName): void;
}
