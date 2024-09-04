<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;

return static function (Configuration $config): Configuration {
    $config->addNamedFilter(NamedFilter::fromString('darkaonline/l5-swagger'));
    $config->addNamedFilter(NamedFilter::fromString('rollbar/rollbar-laravel'));
    $config->addNamedFilter(NamedFilter::fromString('tkaratug/laravel-notification-event-subscriber'));
    $config->addNamedFilter(NamedFilter::fromString('vladimir-yuldashev/laravel-queue-rabbitmq'));

    return $config;
};
