<?php
declare(strict_types=1);


namespace TravelSorter\Api;


use Psr\Container\ContainerInterface;
use TravelSorter\Api\RequestHandler\PostHandler;

class DispatcherFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $handlers = [
            $container->get(PostHandler::class),
        ];

        return new Dispatcher($handlers);
    }

}