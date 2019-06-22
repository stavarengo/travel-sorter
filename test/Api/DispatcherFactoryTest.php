<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\Api;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TravelSorter\Api\Dispatcher;
use TravelSorter\Api\DispatcherFactory;
use TravelSorter\Api\RequestHandler\PostHandler;

class DispatcherFactoryTest extends TestCase
{
    public function testFactoryMustBeInvokable()
    {
        $factory = new DispatcherFactory();

        $this->assertIsCallable($factory);
    }

    public function testHandlersToUse()
    {
        /** @var ContainerInterface|\PHPUnit\Framework\MockObject\MockObject $stubContainer */
        $stubContainer = $this->createMock(ContainerInterface::class);
        $stubContainer->method('get')->willReturnMap([
            [PostHandler::class, $this->createMock(PostHandler::class)]
        ]);


        $factory = new DispatcherFactory();
        /** @var Dispatcher $dispatcher */
        $dispatcher = $factory->__invoke($stubContainer);

        $this->assertInstanceOf(Dispatcher::class, $dispatcher);
        $this->assertCount(1, $dispatcher->getHandlers());
        $this->assertInstanceOf(PostHandler::class, $dispatcher->getHandlers()[0]);
    }
}
