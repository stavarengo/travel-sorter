<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\App\RouteDetector;

use PHPUnit\Framework\TestCase;
use TravelSorter\App\BasePathDetector\BasePathDetectorInterface;
use TravelSorter\App\RouteDetector\RouteDetector;

class RouteDetectorTest extends TestCase
{
    /**
     * @dataProvider getRequestRouteProvider
     */
    public function testGetRequestRoute(string $basePath, string $fullRequestUri, string $expectedRoute)
    {
        $mockBasePathDetector = $this->createMock(BasePathDetectorInterface::class);
        $mockBasePathDetector->method('detect')->willReturn($basePath);

        $this->assertEquals(
            $expectedRoute,
            (new RouteDetector($mockBasePathDetector))->detect($fullRequestUri),
            sprintf('Failed when base path was "%s" and request URI was "%s"', $basePath, $fullRequestUri)
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
