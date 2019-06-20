<?php
declare(strict_types=1);

return [
    // Directories
    'dir.public' => realpath(__DIR__ . '/../public'),
    'dir.server.document_root' => $_SERVER['DOCUMENT_ROOT'],

    // Factories
    \TravelSorter\App\BasePathDetector\BasePathDetectorInterface::class => DI\factory(\TravelSorter\App\BasePathDetector\BasePathDetectorFactory::class),
];