<?php
declare(strict_types=1);

return [
    'container_definitions' => [
    ],
    \TravelSorter\App\BasePathDetector\BasePathDetectorInterface::class => [
        \TravelSorter\App\BasePathDetector\BasePathDetectorInterface::CONFIG_PUBLIC_DIRECTORY => realpath(__DIR__ . '/../../public'),
        \TravelSorter\App\BasePathDetector\BasePathDetectorInterface::CONFIG_PUBLIC_DIRECTORY => $_SERVER['DOCUMENT_ROOT'],
    ]
];