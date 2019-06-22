<?php

namespace TravelSorter\Api;

/**
 * The configuration provider for the App module
 */
class ConfigProvider
{
    public function __invoke()
    {
        return [
            'container_definitions' => $this->getContainerDefinitions(),
        ];
    }

    public function getContainerDefinitions(): array
    {
        return [
            Dispatcher::class => \DI\factory(DispatcherFactory::class),
        ];
    }
}
