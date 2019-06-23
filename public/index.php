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

        $request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();

        $response = $dispatcher->dispatch($request);
        if (!$response) {
            $response = new \GuzzleHttp\Psr7\Response(
                404,
                ['Content-Type' => 'text/html; charset=UTF-8'],
                \GuzzleHttp\Psr7\stream_for('Not found!')
            );
        }
    } catch (\Throwable $e) {
        $response = new \GuzzleHttp\Psr7\Response(
            500,
            ['Content-Type' => 'text/html; charset=UTF-8'],
            \GuzzleHttp\Psr7\stream_for(sprintf("Server error!\n\n%s", $e))
        );
    }

    http_response_code($response->getStatusCode());
    foreach ($response->getHeaders() as $headerName => $hederValue) {
        header(sprintf('%s: %s', $headerName, $response->getHeaderLine($headerName)));
    }
    echo $response->getBody()->getContents();
})();
