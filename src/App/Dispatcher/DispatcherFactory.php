<?php
declare(strict_types=1);


namespace TravelSorter\App\Dispatcher;


use Psr\Container\ContainerInterface;
use TravelSorter\App\ConfigProvider;
use TravelSorter\App\Dispatcher\Exception\MissingConfigEntry;
use TravelSorter\App\RouteDetector\RouteDetectorInterface;

class DispatcherFactory
{
    public function __invoke(
        RouteDetectorInterface $routeDetector,
        ContainerInterface $container
    ) {
        $config = $container->has('config') ? $container->get('config') : null;
        $dispatcherConfig = [];

        if ($config && isset($config[ConfigProvider::class][DispatcherInterface::class])) {
            $dispatcherConfig = $config[ConfigProvider::class][DispatcherInterface::class];
        }

        if (!array_key_exists(DispatcherInterface::REQUEST_HANDLER_MAP, $dispatcherConfig)) {
            throw new MissingConfigEntry(
                sprintf(
                    'Missing config of the "%s". You must add a service in your container called "config" ' .
                    'that must return an `array` with all necessary configurations.',
                    DispatcherInterface::class
                )
            );
        }

        $dispatchersMap = [];
        foreach ($dispatcherConfig[Dispatcher::REQUEST_HANDLER_MAP] as $route => $methodMap) {
            foreach ($methodMap as $method => $requestHandlerServiceName) {
                /** @var DispatcherInterface[] $dispatchers */
                $dispatchersMap[$route][$method] = $container->get($requestHandlerServiceName);
            }
        }

        return new Dispatcher($routeDetector, $dispatchersMap);

    }

}