<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace Symfony\Component\Routing\Loader\Configurator;

use ParkManager\Bundle\CoreBundle\Action\Client\ConfirmPasswordResetAction;
use ParkManager\Bundle\CoreBundle\Action\Client\RequestPasswordResetAction;
use ParkManager\Bundle\CoreBundle\Action\HomepageAction;
use ParkManager\Bundle\CoreBundle\Action\SecurityLoginAction;
use ParkManager\Bundle\CoreBundle\Action\SecurityLogoutAction;

return static function (RoutingConfigurator $routes) {
    $client = $routes->collection('park_manager.client.');

        // Security
        $security = $client->collection('security_');

        $security->add('login', '/login')
            ->controller(SecurityLoginAction::class)
            ->methods(['GET', 'POST']);

        $security->add('logout', '/logout')
            ->controller(SecurityLogoutAction::class)
            ->methods(['GET']);

        $security->add('request_password_reset', '/password-reset')
            ->controller(RequestPasswordResetAction::class)
            ->methods(['GET', 'POST']);

        $security->add('confirm_password_reset', '/password-reset/confirm/{token}')
            ->requirements(['token' => '.+'])// Token can contain slashes
            ->controller(ConfirmPasswordResetAction::class)
            ->methods(['GET', 'POST']);

        $client->add('home', '/')->controller(HomepageAction::class);
};
