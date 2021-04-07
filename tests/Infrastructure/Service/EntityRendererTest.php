<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Tests\Infrastructure\Service;

use ParkManager\Domain\EmailAddress;
use ParkManager\Domain\User\User;
use ParkManager\Domain\User\UserId;
use ParkManager\Infrastructure\Service\EntityRenderer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @internal
 */
final class EntityRendererTest extends TestCase
{
    use ProphecyTrait;

    /** @test */
    public function it_renders_entity_information(): void
    {
        $renderer = new EntityRenderer($this->createTwigEnv(), $this->expectLocaleIsNotChanged());

        $entity = User::register(
            UserId::fromString('9049a7df-c404-43c5-9f1a-279de20f14fc'),
            new EmailAddress('example@example.com'),
            'Freddy Mc. Fee',
            'hacked-by-9lives'
        );

        self::assertEquals('short: Freddy Mc. Fee', $renderer->short($entity, ['expiration' => 'nope']));
        self::assertEquals('detailed: Freddy Mc. Fee.', $renderer->detailed($entity));
        self::assertEquals('detailed: Freddy Mc. Fee.expiration: nope', $renderer->detailed($entity, ['expiration' => 'nope']));

        self::assertEquals('short: Freddy Mc. Fee===============================', $renderer->short($entity, format: 'md'));
        self::assertEquals('detailed: Freddy Mc. Fee===============================', $renderer->detailed($entity, format: 'md'));
    }

    private function createTwigEnv(): Environment
    {
        $loader = new ArrayLoader([
            'entity_rendering/user/user.html.twig' => <<<'TEMPLATE'
                {%- block detailed -%}
                    detailed: {{ entity.displayName }}.
                    {%- if expiration is defined -%}
                        expiration: {{ expiration }}
                    {%- endif -%}
                {%- endblock -%}

                {%- block short -%}
                    short: {{ entity.displayName }}
                {%- endblock -%}
                TEMPLATE,
            'entity_rendering/user/user.md.twig' => <<<'TEMPLATE'
                {%- block detailed -%}
                    detailed: {{ entity.displayName -}}
                    ===============================
                {%- endblock -%}

                {%- block short -%}
                    short: {{ entity.displayName -}}
                    ===============================
                {%- endblock -%}
                TEMPLATE,
        ]);

        return new Environment($loader, ['optimizations' => 0]);
    }

    private function expectLocaleIsNotChanged(): LocaleAwareInterface
    {
        $localeAwareProphecy = $this->prophesize(LocaleAwareInterface::class);
        $localeAwareProphecy->getLocale()->willReturn('en');
        $localeAwareProphecy->setLocale(Argument::any())->shouldNotBeCalled();

        return $localeAwareProphecy->reveal();
    }

    /** @test */
    public function it_resets_locale_after_rendering(): void
    {
        $renderer = new EntityRenderer($this->createTwigEnv(), $this->expectLocaleIsChanged('de'));

        $entity = User::register(
            UserId::fromString('9049a7df-c404-43c5-9f1a-279de20f14fc'),
            new EmailAddress('example@example.com'),
            'Freddy Mc. Fee',
            'hacked-by-9lives'
        );

        self::assertEquals('detailed: Freddy Mc. Fee.', $renderer->detailed($entity, locale: 'de'));
    }

    private function expectLocaleIsChanged(string $tempLocale): LocaleAwareInterface
    {
        $localeAware = $this->createMock(LocaleAwareInterface::class);
        $localeAware->expects(self::once())->method('getLocale')->willReturn('en');
        $localeAware->expects(self::exactly(2))->method('setLocale')->withConsecutive([$tempLocale], ['en']);

        return $localeAware;
    }
}
