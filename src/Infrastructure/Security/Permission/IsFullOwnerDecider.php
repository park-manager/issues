<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Infrastructure\Security\Permission;

use ParkManager\Domain\Organization\OrganizationId;
use ParkManager\Domain\Organization\OrganizationMember;
use ParkManager\Domain\Organization\OrganizationRepository;
use ParkManager\Domain\OwnerRepository;
use ParkManager\Domain\User\UserId;
use ParkManager\Domain\User\UserRepository;
use ParkManager\Infrastructure\Security\Permission;
use ParkManager\Infrastructure\Security\PermissionAccessManager;
use ParkManager\Infrastructure\Security\PermissionDecider;
use ParkManager\Infrastructure\Security\SecurityUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class IsFullOwnerDecider implements PermissionDecider
{
    private OwnerRepository $ownerRepository;
    private OrganizationRepository $organizationRepository;
    private UserRepository $userRepository;

    public function __construct(OwnerRepository $ownerRepository, OrganizationRepository $organizationRepository, UserRepository $userRepository)
    {
        $this->ownerRepository = $ownerRepository;
        $this->organizationRepository = $organizationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param IsFullOwner $permission
     */
    public function decide(Permission $permission, TokenInterface $token, SecurityUser $user, PermissionAccessManager $permissionAccess): int
    {
        // Administrators have full access to entities that are "owned".
        if ($user->isAdmin()) {
            return PermissionDecider::DECIDE_ALLOW;
        }

        $owner = $permission->owner;
        $userId = UserId::fromString($user->getId());

        if ($owner->isUser()) {
            // If the current user is not the Owner abstain access in-case another permission
            // can be more explicit. If all abstain access is denied anyway.
            return $owner->getId()->equals($userId) ? PermissionDecider::DECIDE_ALLOW : PermissionDecider::DECIDE_ABSTAIN;
        }

        if ($owner->isOrganization()) {
            if ($owner->getId()->equals(OrganizationId::fromString(OrganizationId::ADMIN_ORG))) {
                // Given the User is not an Admin and the owner is the Administrator-org access is explicitly denied.
                // Only Admin can access Administrator-org owned spaces. Even sub-resources should not be accessible.
                return PermissionDecider::DECIDE_DENY;
            }

            $org = $this->organizationRepository->get($owner->getId());

            if ($org->hasMember($this->userRepository->get($userId), accessLevel: OrganizationMember::LEVEL_MANAGER)) {
                return PermissionDecider::DECIDE_ALLOW;
            }
        }

        // If the current user is not (an) owner abstain access in-case another permission
        // can be more explicit. If all abstain access is denied anyway.
        return PermissionDecider::DECIDE_ABSTAIN;
    }
}