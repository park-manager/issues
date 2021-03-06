<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\UI\Web\Action\Admin\User;

use Pagerfanta\Pagerfanta;
use ParkManager\Domain\User\UserRepository;
use ParkManager\Infrastructure\Pagerfanta\ResultSetAdapter;
use ParkManager\UI\Web\Response\TwigResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ListUsersAction extends AbstractController
{
    #[Security("is_granted('ROLE_SUPER_ADMIN')")]
    #[Route(path: '/users', methods: ['GET', 'HEAD'], name: 'park_manager.admin.list_users')]
    public function __invoke(Request $request): Response
    {
        $pagerfanta = new Pagerfanta(new ResultSetAdapter($this->get(UserRepository::class)->all()));
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setMaxPerPage(10);

        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        return new TwigResponse('admin/user/list.html.twig', ['users' => $pagerfanta]);
    }

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [UserRepository::class];
    }
}
