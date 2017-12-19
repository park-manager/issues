<?php

declare(strict_types=1);

/*
 * Copyright (c) the Contributors as noted in the AUTHORS file.
 *
 * This file is part of the Park-Manager project.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\CoreBundle\Cli\Command;

use ParkManager\Component\Core\Model\Command\RegisterAdministrator;
use ParkManager\Component\User\Model\UserId;
use Prooph\ServiceBus\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\Argon2iPasswordEncoder;
use Symfony\Component\Validator\Constraints\{Email, NotBlank};
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Sebastiaan Stok <s.stok@rollerworks.net>
 */
final class RegisterAdministratorCommand extends Command
{
    protected static $defaultName = 'park-manager:administrator:register';

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Argon2iPasswordEncoder
     */
    private $passwordEncoder;

    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(ValidatorInterface $validator, Argon2iPasswordEncoder $passwordEncoder, CommandBus $commandBus)
    {
        parent::__construct();

        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
        $this->commandBus = $commandBus;
    }

    protected function configure()
    {
        $this
            ->setDescription('Registers a new Administrator user')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command registers a new Administrator user.
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $firstName = $io->ask('First name');
        $lastName = $io->ask('Last name');
        $email = $io->ask('E-mail address', null, function ($value) {
            $violationList = $this->validator->validate($value, [new NotBlank(), new Email()]);

            if ($violationList->count() > 0) {
                throw new \InvalidArgumentException((string) $violationList);
            }

            return $value;
        });

        $password = $this->passwordEncoder->encodePassword($io->askHidden('Password'), '');
        $this->commandBus->dispatch(
            new RegisterAdministrator(UserId::create()->toString(), $email, $firstName, $lastName, $password)
        );

        $io->success('Administrator was registered.');

        return 0;
    }
}
