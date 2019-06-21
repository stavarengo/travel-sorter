<?php
declare(strict_types=1);


namespace TravelSorter\App\BasePathDetector;


use Psr\Container\ContainerInterface;
use TravelSorter\App\BasePathDetector\Exception\MissingConfigEntry;
use TravelSorter\App\ConfigProvider;

class BasePathDetectorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : null;
        $basePathDetectorConfig = [];

        if ($config && isset($config[ConfigProvider::class][BasePathDetectorInterface::class])) {
            $basePathDetectorConfig = $config[ConfigProvider::class][BasePathDetectorInterface::class];
        }

        if (!array_key_exists(BasePathDetectorInterface::CONFIG_DOCUMENT_ROOT, $basePathDetectorConfig)
            || !array_key_exists(BasePathDetectorInterface::CONFIG_PUBLIC_DIRECTORY, $basePathDetectorConfig)
        ) {
            throw new MissingConfigEntry(
                sprintf(
                    'Missing config of the "%s". You must add a service in your container called "config" that must return an `array` with all necessary configurations.',
                    BasePathDetectorInterface::class
                )
            );
        }

        /** @var string $documentRoot */
        $documentRoot = $basePathDetectorConfig[BasePathDetectorInterface::CONFIG_DOCUMENT_ROOT];
        /** @var string $publicDirectoryPath */
        $publicDirectoryPath = $basePathDetectorConfig[BasePathDetectorInterface::CONFIG_PUBLIC_DIRECTORY];

        return new BasePathDetector($documentRoot, $publicDirectoryPath);
    }

}