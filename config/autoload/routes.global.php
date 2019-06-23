<?php
declare(strict_types=1);

return [
    \TravelSorter\App\ConfigProvider::class => [
        \TravelSorter\App\Dispatcher\DispatcherInterface::class => [
            \TravelSorter\App\Dispatcher\DispatcherInterface::REQUEST_HANDLER_MAP => [
                '/api/sort' => [
                    'POST' => \TravelSorter\Api\RequestHandler\PostHandler::class,
                ]
            ],
        ],
    ],
];