<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\UI\Web\Action\Admin\Webhosting\Plan;

use ParkManager\UI\Web\Form\Type\Webhosting\Plan\AddWebhostingPlanForm;
use ParkManager\UI\Web\Response\RouteRedirectResponse;
use ParkManager\UI\Web\Response\TwigResponse;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class AddWebhostingPlan
{
    #[Route(path: 'webhosting/plan/add', name: 'park_manager.admin.webhosting.plan.add', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, FormFactoryInterface $formFactory): RouteRedirectResponse | TwigResponse
    {
        $form = $formFactory->create(AddWebhostingPlanForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return RouteRedirectResponse::toRoute('park_manager.admin.webhosting.plan.list')->withFlash(type: 'success', message: 'flash.webhosting_plan.added');
        }

        return new TwigResponse('admin/webhosting/plan/add.html.twig', $form);
    }
}
