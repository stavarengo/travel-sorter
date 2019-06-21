<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\App\Dispatcher;

use PHPUnit\Framework\TestCase;
use TravelSorter\App\BasePathDetector\BasePathDetectorInterface;
use TravelSorter\App\Dispatcher\DispatcherAggregate;
use TravelSorter\App\Dispatcher\DispatcherInterface;
use TravelSorter\App\Dispatcher\DispatcherResponse;

class DispatcherAggregateTest extends TestCase
{
    public function testWithNoDispatchers()
    {
        $this->assertNull((new DispatcherAggregate($this->mockBasePathDetector('/'), []))->dispatch('/'));
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
        /** @var DispatcherInterface $stubDispatcher */
        $stubDispatcher = $this->createMock(DispatcherInterface::class);
        $stubDispatcher->method('dispatch')
            ->willReturn(new DispatcherResponse(200, '', []));

        $dispatchers = [$stubDispatcher];

        $this->assertNotNull((new DispatcherAggregate($this->mockBasePathDetector('/'), $dispatchers))->dispatch('/'));
    }

    public function testWithDispatchersThatReturnNullValues()
    {
        /** @var DispatcherInterface $stubDispatcher */
        $stubDispatcher = $this->createMock(DispatcherInterface::class);
        $stubDispatcher->method('dispatch')
            ->willReturn(null);

        $dispatchers = [$stubDispatcher];

        $this->assertNull((new DispatcherAggregate($this->mockBasePathDetector('/'), $dispatchers))->dispatch('/'));
    }

    /**
     * @dataProvider getRequestRouteProvider
     */
    public function testGetRequestRoute(string $basePath, string $fullRequestUri, string $expectedRoute)
    {
        $failMsg = 'Failed when base path was "%s" and request URI was "%s"';
        $this->assertEquals(
            $expectedRoute,
            DispatcherAggregate::getRequestRoute($basePath, $fullRequestUri),
            sprintf($failMsg, $basePath, $fullRequestUri)
        );
    }

    public function getRequestRouteProvider(): array
    {
        return [
            // BASE PATH, FULL REQUEST URI, EXPECTED RESULT
            ['', '/?param=value#component', '/'],
            ['/', '/?param=value#component', '/'],
            ['/public', '/public/?param=value#component', '/'],
            ['/public', '/public?param=value#component', '/'],
            ['/public/', '/public/?param=value#component', '/'],
            ['/html/public', '/html/public/?param=value#component', '/'],
            ['/html/public', '/html/public?param=value#component', '/'],
            ['/html/public/', '/html/public/?param=value#component', '/'],
            ['', '/api/sort?param=value#component', '/api/sort'],
            ['/', '/api/sort?param=value#component', '/api/sort'],
            ['/public', '/public/api/sort?param=value#component', '/api/sort'],
            ['/public/', '/public/api/sort?param=value#component', '/api/sort'],
            ['/html/public', '/html/public/api/sort?param=value#component', '/api/sort'],
            ['/html/public/', '/html/public/api/sort?param=value#component', '/api/sort'],
        ];
    }
}
