<?php
declare(strict_types=1);

return [
    'container_definitions' => [
    ],
    \TravelSorter\App\ConfigProvider::class => [
        \TravelSorter\App\Dispatcher\DispatcherAggregate::class => [
            \TravelSorter\App\Dispatcher\DispatcherAggregate::CONFIG_DISPATCHERS => [
                \TravelSorter\Api\Dispatcher::class,
            ],
        ],
    ],
];