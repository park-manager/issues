<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\CoreBundle\Tests\Model\Client;

use Assert\AssertionFailedException;
use DateTimeImmutable;
use ParkManager\Bundle\CoreBundle\Model\Client\Client;
use ParkManager\Bundle\CoreBundle\Model\Client\ClientId;
use ParkManager\Bundle\CoreBundle\Model\Client\Event\ClientNameWasChanged;
use ParkManager\Bundle\CoreBundle\Model\Client\Event\ClientPasswordResetWasRequested;
use ParkManager\Bundle\CoreBundle\Model\Client\Event\ClientPasswordWasChanged;
use ParkManager\Bundle\CoreBundle\Model\Client\Exception\EmailChangeConfirmationRejected;
use ParkManager\Bundle\CoreBundle\Model\Client\Exception\PasswordResetConfirmationRejected;
use ParkManager\Bundle\CoreBundle\Model\EmailAddress;
use ParkManager\Bundle\CoreBundle\Test\Domain\EventsRecordingEntityAssertionTrait;
use PHPUnit\Framework\TestCase;
use Rollerworks\Component\SplitToken\FakeSplitTokenFactory;
use Rollerworks\Component\SplitToken\SplitToken;

/**
 * @internal
 */
final class ClientTest extends TestCase
{
    use EventsRecordingEntityAssertionTrait;

    private const ID1 = '930c3fd0-3bd1-11e7-bb9b-acdc32b58315';

    /** @var FakeSplitTokenFactory */
    private $splitTokenFactory;

    protected function setUp(): void
    {
        $this->splitTokenFactory = FakeSplitTokenFactory::instance();
    }

    /** @test */
    public function gets_registered(): void
    {
        $client = Client::register(
            $id = ClientId::fromString(self::ID1),
            $email = new EmailAddress('John@example.com'),
            'Jane Doe'
        );

        static::assertEquals($id, $client->getId());
        static::assertEquals($email, $client->getEmail());
    }

    /** @test */
    public function change_email(): void
    {
        $client = $this->registerClient();
        $client->changeEmail($email = new EmailAddress('Doh@example.com'));

        static::assertEquals($email, $client->getEmail());
    }

    private function registerClient(?string $password = null): Client
    {
        $client = Client::register(ClientId::fromString(self::ID1), new EmailAddress('john@example.com'), 'Laural Doe');
        $client->changePassword($password);
        $client->releaseEvents();

        return $client;
    }

    /** @test */
    public function change_dislay_name(): void
    {
        $client = $this->registerClient();
        $client->changeName('Jenny');

        self::assertDomainEvents($client, [new ClientNameWasChanged($client->getId(), 'Jenny')]);
    }

    /** @test */
    public function disable_access(): void
    {
        $client = $this->registerClient();
        $client->disable();

        static::assertFalse($client->isEnabled());
    }

    /** @test */
    public function enable_access_after_disabled(): void
    {
        $client = $this->registerClient();
        $client->disable();
        $client->enable();

        static::assertTrue($client->isEnabled());
    }

    /** @test */
    public function change_password(): void
    {
        $client = $this->registerClient();

        $client->changePassword('security-is-null');

        self::assertDomainEvents($client, [new ClientPasswordWasChanged($client->getId(), 'security-is-null')]);
    }

    /** @test */
    public function change_password_to_null(): void
    {
        $client = $this->registerClient('security-is-null');
        $client->changePassword(null);

        self::assertDomainEvents($client, [new ClientPasswordWasChanged($client->getId(), null)]);
    }

    /** @test */
    public function password_cannot_be_empty_when_string(): void
    {
        $client = $this->registerClient();

        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessage('Password can only null or a non-empty string.');

        $client->changePassword('');
    }

    /** @test */
    public function request_email_change(): void
    {
        $token = $this->createTimeLimitedSplitToken(new DateTimeImmutable('+ 5 minutes UTC'));
        $client = $this->registerClient();

        static::assertTrue($client->requestEmailChange($email = new EmailAddress('Doh@example.com'), $token));
        static::assertEquals(new EmailAddress('john@example.com'), $client->getEmail());
    }

    private function createTimeLimitedSplitToken(DateTimeImmutable $expiresAt): SplitToken
    {
        return $this->splitTokenFactory->generate()->expireAt($expiresAt);
    }

    /** @test */
    public function ignores_email_change_token_when_already_set_with_same_information(): void
    {
        $token = $this->createTimeLimitedSplitToken(new DateTimeImmutable('+ 5 minutes UTC'));
        $client = $this->registerClient();

        static::assertTrue($client->requestEmailChange($email = new EmailAddress('Doh@example.com'), $token));
        static::assertFalse($client->requestEmailChange($email, $token));
    }

    /** @test */
    public function changes_email_when_confirmation_token_is_correct(): void
    {
        $token = $this->createTimeLimitedSplitToken(new DateTimeImmutable('+ 5 minutes UTC'));
        $client = $this->registerClient();
        $client->requestEmailChange($email = new EmailAddress('Doh@example.com'), $token);

        $client->confirmEmailChange($this->getTokenString($token));

        // Second usage is prohibited, so try a second time.
        $this->assertEmailChangeThrowsRejected($client, $token);

        static::assertEquals($email, $client->getEmail());
    }

    private function assertEmailChangeThrowsRejected(Client $client, SplitToken $token): void
    {
        try {
            $client->confirmEmailChange($token);

            static::fail('EmailChangeConfirmationRejected was expected');
        } catch (EmailChangeConfirmationRejected $e) {
            $this->addToAssertionCount(1);
        }
    }

    private function getTokenString(SplitToken $token): SplitToken
    {
        return $this->splitTokenFactory->fromString($token->token()->getString());
    }

    /** @test */
    public function rejects_email_change_confirmation_when_token_is_invalid(): void
    {
        $correctToken = $this->createTimeLimitedSplitToken(new DateTimeImmutable('+ 5 minutes UTC'));
        $invalidToken = $this->generateSecondToken();

        $client = $this->registerClient();
        $client->requestEmailChange(new EmailAddress('Doh@example.com'), $correctToken);

        $this->assertEmailChangeThrowsRejected($client, $invalidToken);

        // Second attempt is prohibited, so try a second time (with correct token)!
        $this->assertEmailChangeThrowsRejected($client, $correctToken);

        static::assertEquals(new EmailAddress('john@example.com'), $client->getEmail());
    }

    private function generateSecondToken(): SplitToken
    {
        return FakeSplitTokenFactory::instance(\str_repeat('na', SplitToken::TOKEN_CHAR_LENGTH))->generate();
    }

    /** @test */
    public function rejects_email_change_confirmation_when_token_was_not_set(): void
    {
        $token = FakeSplitTokenFactory::instance()->generate();
        $client = $this->registerClient();

        $this->assertEmailChangeThrowsRejected($client, $token);
        static::assertEquals(new EmailAddress('john@example.com'), $client->getEmail());
    }

    /** @test */
    public function request_password_reset_confirmation_token(): void
    {
        $token = $this->createTimeLimitedSplitToken(new DateTimeImmutable('+ 5 minutes UTC'));
        $client = $this->registerClient('pass-my-word');

        static::assertTrue($client->requestPasswordReset($token));
    }

    /** @test */
    public function reject_password_reset_confirmation_when_token_already_set_with_and_not_expired(): void
    {
        $token = $this->createTimeLimitedSplitToken(new DateTimeImmutable('+ 5 minutes UTC'));
        $client = $this->registerClient('pass-my-word');

        static::assertTrue($client->requestPasswordReset($token));
        static::assertFalse($client->requestPasswordReset($token));
    }

    /** @test */
    public function changes_password_when_token_is_correct(): void
    {
        $token = $this->createTimeLimitedSplitToken(new DateTimeImmutable('+ 5 minutes UTC'));
        $client = $this->registerClient('pass-my-word');
        $client->requestPasswordReset($token);
        $id = $client->getId();

        $client->confirmPasswordReset($token2 = $this->getTokenString($token), 'new-password');

        self::assertDomainEvents(
            $client,
            [
                new ClientPasswordResetWasRequested($id, $token),
                new ClientPasswordWasChanged($id, 'new-password'),
            ]
        );
    }

    /** @test */
    public function password_reset_is_rejected_for_invalid_token(): void
    {
        $correctToken = $this->createTimeLimitedSplitToken(new DateTimeImmutable('+ 5 minutes UTC'));
        $invalidToken = $this->generateSecondToken();

        $client = $this->registerClient('pass-my-word');
        $client->requestPasswordReset($correctToken);
        $client->releaseEvents();

        $this->assertPasswordResetThrowsRejected($client, $invalidToken);

        // Second attempt is prohibited, so try a second time (with correct token)!
        $this->assertPasswordResetThrowsRejected($client, $correctToken);

        self::assertDomainEvents($client, []);
    }

    private function assertPasswordResetThrowsRejected(Client $client, SplitToken $token): void
    {
        try {
            $client->confirmPasswordReset($token, 'new-password');

            static::fail('PasswordResetConfirmationRejected was expected');
        } catch (PasswordResetConfirmationRejected $e) {
            $this->addToAssertionCount(1);
        }
    }

    /** @test */
    public function password_reset_is_rejected_when_no_token_was_set(): void
    {
        $client = $this->registerClient('pass-my-word');

        $this->assertPasswordResetThrowsRejected($client, $this->splitTokenFactory->generate());
        self::assertNoDomainEvents($client);
    }

    /** @test */
    public function password_reset_is_rejected_when_token_has_expired(): void
    {
        $token = $this->createTimeLimitedSplitToken(new DateTimeImmutable('- 5 minutes UTC'));
        $client = $this->registerClient('pass-my-word');
        $client->requestPasswordReset($token);

        $this->assertPasswordResetThrowsRejected($client, $token);
        self::assertDomainEvents(
            $client,
            [new ClientPasswordResetWasRequested($client->getId(), $token)]
        );
    }
}
