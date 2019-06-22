<?php
declare(strict_types=1);


namespace TravelSorter\App\Dispatcher;


use Psr\Container\ContainerInterface;
use TravelSorter\App\BasePathDetector\BasePathDetectorInterface;
use TravelSorter\App\ConfigProvider;
use TravelSorter\App\Dispatcher\Exception\MissingConfigEntry;

class DispatcherAggregateFactory
{
    public function __invoke(BasePathDetectorInterface $basePathDetector, ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : null;
        $dispatcherConfig = [];

        if ($config && isset($config[ConfigProvider::class][DispatcherAggregate::class])) {
            $dispatcherConfig = $config[ConfigProvider::class][DispatcherAggregate::class];
        }

        if (!array_key_exists(DispatcherAggregate::CONFIG_DISPATCHERS, $dispatcherConfig)) {
            throw new MissingConfigEntry(
                sprintf(
                    'Missing config of the "%s". You must add a service in your container called "config" that must return an `array` with all necessary configurations.',
                    DispatcherAggregate::class
                )
            );
        }

        /** @var DispatcherInterface[] $dispatchers */
        $dispatchers = array_map(
            function ($dispatcher) use ($container) {
                return $container->get($dispatcher);
            },
            $dispatcherConfig[DispatcherAggregate::CONFIG_DISPATCHERS]
        );

        return new DispatcherAggregate($basePathDetector, $dispatchers);

    }

}