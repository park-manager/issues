<?php

require_once __DIR__.'/vendor/sllh/php-cs-fixer-styleci-bridge/autoload.php';

use SLLH\StyleCIBridge\ConfigBridge;
use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;

$header = <<<EOF
Copyright (c) the Contributors as noted in the AUTHORS file.

This file is part of the Park-Manager project.

This Source Code Form is subject to the terms of the Mozilla Public
License, v. 2.0. If a copy of the MPL was not distributed with this
file, You can obtain one at http://mozilla.org/MPL/2.0/.
EOF;

// PHP-CS-Fixer 1.x
if (class_exists('Symfony\CS\Fixer\Contrib\HeaderCommentFixer')) {
    \Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);
}

$config = ConfigBridge::create()
    ->setUsingCache(true)
;

// PHP-CS-Fixer 2.x
if (method_exists($config, 'setRules')) {
    $config->setRules(
        array_merge(
            $config->getRules(),
            [
                'header_comment' => ['header' => $header],
            ]
        )
    );
}

return $config;

