<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Tests\Mock\Domain\Organization;

use ParkManager\Domain\Organization\Exception\OrganizationNotFound;
use ParkManager\Domain\Organization\Organization;
use ParkManager\Domain\Organization\OrganizationId;
use ParkManager\Domain\Organization\OrganizationRepository;
use ParkManager\Domain\ResultSet;
use ParkManager\Domain\User\UserId;
use ParkManager\Domain\User\UserRepository;
use ParkManager\Tests\Mock\Domain\MockRepository;

final class OrganizationRepositoryMock implements OrganizationRepository
{
    use MockRepository {
        __construct as mockConstructor;
    }

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository, array $initialEntities = [])
    {
        $this->mockConstructor($initialEntities);

        $this->userRepository = $userRepository;
    }

    protected function throwOnNotFound($key): void
    {
        throw OrganizationNotFound::withId($key);
    }

    public function get(OrganizationId $id): Organization
    {
        return $this->mockDoGetById($id);
    }

    public function all(): ResultSet
    {
        return $this->mockDoGetAll();
    }

    public function allAccessibleBy(UserId $userId): ResultSet
    {
        return $this->mockDoGetMultiByCondition(static fn (Organization $organization) => $organization->hasMember($this->userRepository->get($userId)));
    }

    public function save(Organization $organization): void
    {
        $this->mockDoSave($organization);
    }

    public function remove(Organization $organization): void
    {
        $this->mockDoRemove($organization);
    }
}