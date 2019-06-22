<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\App\Dispatcher;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TravelSorter\App\BasePathDetector\BasePathDetectorInterface;
use TravelSorter\App\Dispatcher\DispatcherAggregate;
use TravelSorter\App\Dispatcher\DispatcherInterface;
use TravelSorter\App\Dispatcher\DispatcherResponse;

class DispatcherAggregateTest extends TestCase
{
    public function testWithNoDispatchers()
    {
        $this->assertNull((new DispatcherAggregate($this->mockBasePathDetector('/'), []))->dispatch('/', 'GET'));
    }

    private function mockBasePathDetector(string $basePath): BasePathDetectorInterface
    {
        /** @var BasePathDetectorInterface|\PHPUnit\Framework\MockObject\MockObject $stubBasePathDetector */
        $stubBasePathDetector = $this->createMock(BasePathDetectorInterface::class);

        $stubBasePathDetector->method('detect')->willReturn($basePath);

        return $stubBasePathDetector;
    }

    public function testWithDispatchersThatReturnNonNullValues()
    {
        /** @var DispatcherInterface|MockObject $stubDispatcher */
        $stubDispatcher = $this->createMock(DispatcherInterface::class);
        $stubDispatcher->method('dispatch')
            ->willReturn(new DispatcherResponse(200, '', []));

        $dispatchers = [$stubDispatcher];

        $this->assertNotNull((new DispatcherAggregate($this->mockBasePathDetector('/'), $dispatchers))->dispatch('/', 'GET'));
    }

    public function testWithDispatchersThatReturnNullValues()
    {
        /** @var DispatcherInterface|MockObject $stubDispatcher */
        $stubDispatcher = $this->createMock(DispatcherInterface::class);
        $stubDispatcher->method('dispatch')
            ->willReturn(null);

        $dispatchers = [$stubDispatcher];

        $this->assertNull((new DispatcherAggregate($this->mockBasePathDetector('/'), $dispatchers))->dispatch('/', 'GET'));
    }
}
