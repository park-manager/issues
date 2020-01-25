<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\CoreBundle\Tests\UseCase\Client;

use DateTimeImmutable;
use ParkManager\Bundle\CoreBundle\Mailer\Client\EmailAddressChangeRequestMailer;
use ParkManager\Bundle\CoreBundle\Test\Model\Repository\ClientRepositoryMock;
use ParkManager\Bundle\CoreBundle\UseCase\Client\RequestEmailAddressChange;
use ParkManager\Bundle\CoreBundle\UseCase\Client\RequestEmailAddressChangeHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Rollerworks\Component\SplitToken\FakeSplitTokenFactory;
use Rollerworks\Component\SplitToken\SplitToken;

/**
 * @internal
 */
final class RequestEmailAddressChangeTest extends TestCase
{
    private const USER_ID = '01dd5964-5426-11e7-be03-acbc32b58315';

    /** @var SplitToken */
    private $fullToken;

    /** @var SplitToken */
    private $token;

    protected function setUp(): void
    {
        $this->fullToken = FakeSplitTokenFactory::instance()->generate();
        $this->token = FakeSplitTokenFactory::instance()->fromString($this->fullToken->token()->getString());
    }

    /** @test */
    public function it_handles_email_address_change_request(): void
    {
        $handler = new RequestEmailAddressChangeHandler(
            $repository = new ClientRepositoryMock([$client = ClientRepositoryMock::createClient()]),
            $this->createConfirmationMailer('John2@example.com'),
            FakeSplitTokenFactory::instance()
        );

        $handler(new RequestEmailAddressChange(self::USER_ID, 'John2@example.com'));

        $repository->assertEntitiesWereSaved();
        $token = $client->getEmailAddressChangeToken();
        static::assertEquals(['email' => 'John2@example.com'], $token->metadata());
        static::assertFalse($token->isExpired(new DateTimeImmutable('+ 5 seconds')));
        static::assertTrue($token->isExpired(new DateTimeImmutable('+ 3700 seconds')));
    }

    /** @test */
    public function it_handles_email_address_change_request_with_email_address_already_in_use(): void
    {
        $handler = new RequestEmailAddressChangeHandler(
            $repository = new ClientRepositoryMock([
                ClientRepositoryMock::createClient('janE@example.com'),
                $client2 = ClientRepositoryMock::createClient('John2@example.com'),
            ]),
            $this->expectNoConfirmationIsSendMailer(),
            FakeSplitTokenFactory::instance()
        );

        $handler(new RequestEmailAddressChange(self::USER_ID, 'John2@example.com'));

        $repository->assertNoEntitiesWereSaved();
    }

    private function createConfirmationMailer(string $email): EmailAddressChangeRequestMailer
    {
        $confirmationMailerProphecy = $this->prophesize(EmailAddressChangeRequestMailer::class);
        $confirmationMailerProphecy->send(
            $email,
            Argument::that(
                static function (SplitToken $splitToken) {
                    return $splitToken->token()->getString() !== '';
                }
            )
        )->shouldBeCalledTimes(1);

        return $confirmationMailerProphecy->reveal();
    }

    private function expectNoConfirmationIsSendMailer(): EmailAddressChangeRequestMailer
    {
        $confirmationMailerProphecy = $this->prophesize(EmailAddressChangeRequestMailer::class);
        $confirmationMailerProphecy->send(Argument::any(), Argument::any())->shouldNotBeCalled();

        return $confirmationMailerProphecy->reveal();
    }
}
