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

    try {
        /** @var \TravelSorter\App\Dispatcher\DispatcherInterface $dispatcher */
        $dispatcher = $container->get(\TravelSorter\App\Dispatcher\DispatcherInterface::class);
        /** @var \TravelSorter\App\RouteDetector\RouteDetectorInterface $routeDetector */
        $routeDetector = $container->get(\TravelSorter\App\RouteDetector\RouteDetectorInterface::class);

        $requestRoute = $routeDetector->detect($_SERVER['REQUEST_URI']);
        $dispatcherResponse = $dispatcher->dispatch($requestRoute, $_SERVER['REQUEST_METHOD']);
        if (!$dispatcherResponse) {
            $dispatcherResponse = new \TravelSorter\App\Dispatcher\DispatcherResponse(
                404,
                'Not found!',
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }
    } catch (\Throwable $e) {
        $dispatcherResponse = $dispatcherResponse = new \TravelSorter\App\Dispatcher\DispatcherResponse(
            500,
            sprintf("Server error!\n\n%s", $e),
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    http_response_code($dispatcherResponse->getStatusCode());
    foreach ($dispatcherResponse->getHeaders() as $headerName => $hederValue) {
        header("$headerName: $hederValue");
    }
    echo $dispatcherResponse->getContent();
})();
