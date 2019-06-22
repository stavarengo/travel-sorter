<?php

namespace TravelSorter\App;

/**
 * The configuration provider for the App module
 */
class ConfigProvider
{
    public function __invoke()
    {
        return [
            'container_definitions' => $this->getContainerDefinitions(),
            self::class => [
                BasePathDetector\BasePathDetectorInterface::class => $this->getBasePathDetectorConfig(),
                Dispatcher\DispatcherAggregate::class => $this->getDispatcherAggregateConfig(),
            ]
        ];
    }

    public function getContainerDefinitions(): array
    {
        return [
            BasePathDetector\BasePathDetectorInterface::class => \DI\factory(BasePathDetector\BasePathDetectorFactory::class),
            Dispatcher\DispatcherInterface::class => \DI\factory(Dispatcher\DispatcherAggregateFactory::class),
            TicketsSorter\TicketsSorterInterface::class => \DI\autowire(TicketsSorter\SortByOriginAlphabetically::class),
            RouteDetector\RouteDetectorInterface::class => \DI\autowire(RouteDetector\RouteDetector::class),
        ];
    }

    public function getBasePathDetectorConfig(): array
    {
        return [
            BasePathDetector\BasePathDetectorInterface::CONFIG_PUBLIC_DIRECTORY => realpath(__DIR__ . '/../../public'),
            BasePathDetector\BasePathDetectorInterface::CONFIG_DOCUMENT_ROOT => $_SERVER['DOCUMENT_ROOT'],
        ];
    }

    public function getDispatcherAggregateConfig(): array
    {
        return [
            Dispatcher\DispatcherAggregate::CONFIG_DISPATCHERS => [],
        ];
    }

}
