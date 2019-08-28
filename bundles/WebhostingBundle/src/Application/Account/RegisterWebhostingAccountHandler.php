<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Application\Account;

use ParkManager\Bundle\WebhostingBundle\Domain\Account\WebhostingAccount;
use ParkManager\Bundle\WebhostingBundle\Domain\Account\WebhostingAccountRepository;
use ParkManager\Bundle\WebhostingBundle\Domain\DomainName\Exception\DomainNameAlreadyInUse;
use ParkManager\Bundle\WebhostingBundle\Domain\DomainName\WebhostingDomainName;
use ParkManager\Bundle\WebhostingBundle\Domain\DomainName\WebhostingDomainNameRepository;
use ParkManager\Bundle\WebhostingBundle\Domain\Package\WebhostingPackageRepository;

final class RegisterWebhostingAccountHandler
{
    private $accountRepository;
    private $packageRepository;
    private $domainNameRepository;

    public function __construct(WebhostingAccountRepository $accountRepository, WebhostingPackageRepository $packageRepository, WebhostingDomainNameRepository $domainNameRepository)
    {
        $this->accountRepository    = $accountRepository;
        $this->packageRepository    = $packageRepository;
        $this->domainNameRepository = $domainNameRepository;
    }

    public function __invoke(RegisterWebhostingAccount $command): void
    {
        $domainName = $command->domainName();
        $packageId  = $command->package();

        $currentRegistration = $this->domainNameRepository->findByFullName($domainName);

        if ($currentRegistration !== null) {
            throw DomainNameAlreadyInUse::byAccountId($domainName, $currentRegistration->account()->id());
        }

        if ($packageId !== null) {
            $account = WebhostingAccount::register(
                $command->id(),
                $command->owner(),
                $this->packageRepository->get($packageId)
            );
        } else {
            $account = WebhostingAccount::registerWithCustomCapabilities(
                $command->id(),
                $command->owner(),
                $command->customCapabilities()
            );
        }

        $primaryDomainName = WebhostingDomainName::registerPrimary($account, $domainName);

        $this->accountRepository->save($account);
        $this->domainNameRepository->save($primaryDomainName);
    }
}
