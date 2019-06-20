<?php

declare(strict_types=1);

chdir(dirname(__DIR__));

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    $msg = 'Did you forgot to run `composer install`?' . PHP_EOL . 'Unable to load the "./vendor/autoload.php".';
    http_response_code(500);
    echo "<pre>$msg</pre>";
    throw new RuntimeException($msg);
}
require __DIR__ . '/../vendor/autoload.php';

/**
 * I'm using a self-called anonymous function to create its own scope and keep the the variables created here away from
 * the global scope.
 */
(function () {
    /** @var \Psr\Container\ContainerInterface $container */
    $container = include_once __DIR__ . '/../config/container.php';
    /** @var \TravelSorter\App\BasePathDetector\BasePathDetectorInterface $basePathDetector */
    $basePathDetector = $container->get(\TravelSorter\App\BasePathDetector\BasePathDetectorInterface::class);

    http_response_code(200);
    echo 'It works!<br>';
    echo sprintf('Base path is: <code>%s</code>', $basePathDetector->detect());
})();
