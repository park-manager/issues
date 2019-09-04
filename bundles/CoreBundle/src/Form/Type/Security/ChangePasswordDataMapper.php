<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\CoreBundle\Form\Type\Security;

use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @internal
 */
final class ChangePasswordDataMapper implements DataMapperInterface
{
    /** @var callable */
    private $commandBuilder;

    public function __construct(callable $commandBuilder)
    {
        $this->commandBuilder = $commandBuilder;
    }

    public function mapDataToForms($data, $forms): void
    {
        /** @var FormInterface[] $formsArray */
        $formsArray = \iterator_to_array($forms);

        $formsArray['user_id']->setData($formsArray['user_id']->getConfig()->getData());
        $formsArray['password']->setData($data['password'] ?? '');
    }

    public function mapFormsToData($forms, &$data): void
    {
        /** @var FormInterface[] $formsArray */
        $formsArray = \iterator_to_array($forms);

        $data = ($this->commandBuilder)(
            (string) $formsArray['user_id']->getConfig()->getData(),
            (string) $formsArray['password']->getData()
        );
    }
}
