<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\App\RouteDetector;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use TravelSorter\App\BasePathDetector\BasePathDetectorInterface;
use TravelSorter\App\RouteDetector\RouteDetector;

class RouteDetectorTest extends TestCase
{
    /**
     * @dataProvider getRequestRouteProvider
     */
    public function testGetRequestRoute(string $basePath, string $uriPath, string $expectedRoute)
    {
        $mockBasePathDetector = $this->createMock(BasePathDetectorInterface::class);
        $mockBasePathDetector->method('detect')->willReturn($basePath);

        $mockUri = $this->createMock(UriInterface::class);
        $mockUri->method('getPath')->willReturn($uriPath);

        $this->assertEquals($expectedRoute, (new RouteDetector($mockBasePathDetector))->detect($mockUri));
    }

    public function getRequestRouteProvider(): array
    {
        return [
            // BASE PATH, URI PATH, EXPECTED RESULT
            ['', '/', '/'],
            ['/public', '/public/', '/'],
            ['/public', '/public', '/'],
            ['/public/', '/public/', '/'],
            ['/html/public', '/html/public/', '/'],
            ['/html/public', '/html/public', '/'],
            ['/html/public/', '/html/public/', '/'],
            ['', '/api/sort', '/api/sort'],
            ['/', '/api/sort', '/api/sort'],
            ['/public', '/public/api/sort', '/api/sort'],
            ['/public/', '/public/api/sort', '/api/sort'],
            ['/html/public', '/html/public/api/sort', '/api/sort'],
            ['/html/public/', '/html/public/api/sort', '/api/sort'],
        ];
    }

}
