<?php
declare(strict_types=1);


namespace TravelSorter\App\BasePathDetector;


use Psr\Container\ContainerInterface;

class BasePathDetectorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var string $documentRoot */
        $documentRoot = $container->get('dir.server.document_root');
        /** @var string $publicDirectoryPath */
        $publicDirectoryPath = $container->get('dir.public');

        return new BasePathDetector($documentRoot, $publicDirectoryPath);
    }

}