<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Infrastructure\Twig;

use ParkManager\Domain\User\User;
use ParkManager\Domain\User\UserId;
use ParkManager\Domain\User\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\EscaperExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class ParkManagerExtension extends AbstractExtension
{
    private TranslatorInterface $translator;
    private TranslatorInterface $argumentsTranslator;
    private TokenStorageInterface $tokenStorage;
    private UserRepository $userRepository;

    public function __construct(TranslatorInterface $translator, TokenStorageInterface $tokenStorage, UserRepository $userRepository)
    {
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
        $this->userRepository = $userRepository;

        $this->argumentsTranslator = new class($translator) implements TranslatorInterface {
            private TranslatorInterface $wrappedTranslator;
            private Environment $env;

            public function __construct(TranslatorInterface $wrappedTranslator)
            {
                $this->wrappedTranslator = $wrappedTranslator;
            }

            public function setEnv(Environment $env): void
            {
                $this->env = $env;
            }

            public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
            {
                foreach ($parameters as $name => $value) {
                    $parameters[$name] = twig_escape_filter($this->env, $value);
                }

                return $this->wrappedTranslator->trans($id, $parameters, $domain, $locale);
            }
        };
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('trans_safe', [$this, 'trans'], ['needs_environment' => true, 'is_safe' => ['all']]),
            new TwigFilter('merge_attr_class', [$this, 'mergeAttrClass']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_current_user', [$this, 'getCurrentUser']),
        ];
    }

    /**
     * @param string|\Stringable|TranslatableInterface|null $message
     * @param array|string                                  $arguments Can be the locale as a string when $message is a TranslatableInterface
     */
    public function trans(Environment $env, $message, $arguments = [], string $domain = null, string $locale = null, int $count = null): string
    {
        if ($message instanceof TranslatableInterface) {
            if ($arguments !== [] && ! \is_string($arguments)) {
                throw new \TypeError(\sprintf('Argument 2 passed to "%s()" must be a locale passed as a string when the message is a "%s", "%s" given.', __METHOD__, TranslatableInterface::class, \get_debug_type($arguments)));
            }

            $this->argumentsTranslator->setEnv($env);

            return $message->trans($this->argumentsTranslator, $locale ?? (\is_string($arguments) ? $arguments : null));
        }

        if (! \is_array($arguments)) {
            throw new \TypeError(\sprintf('Unless the message is a "%s", argument 2 passed to "%s()" must be an array of parameters, "%s" given.', TranslatableInterface::class, __METHOD__, \get_debug_type($arguments)));
        }

        $message = (string) $message;

        if ($message === '') {
            return '';
        }

        foreach ($arguments as $name => $value) {
            $arguments[$name] = twig_escape_filter($env, $value);
        }

        if ($count !== null) {
            $arguments['%count%'] = $count;
        }

        return $this->translator->trans($message, $arguments, $domain, $locale);
    }

    public function mergeAttrClass(array $attributes, string $class, bool $append = false): array
    {
        if (! isset($attributes['class'])) {
            $attributes['class'] = '';
        }

        if ($append) {
            $attributes['class'] .= ' ' . $class;
        } else {
            $attributes['class'] = $class . ' ' . $attributes['class'];
        }

        $attributes['class'] = \trim($attributes['class']);

        return $attributes;
    }

    public function getCurrentUser(): User
    {
        static $currentToken, $currentUser;

        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            throw new AccessDeniedException();
        }

        if ($currentToken !== $token) {
            $currentToken = $token;
            $currentUser = $this->userRepository->get(UserId::fromString($token->getUsername()));
        }

        return $currentUser;
    }
}

// Force autoloading of the EscaperExtension as we need the twig_escape_filter() function
\class_exists(EscaperExtension::class);
