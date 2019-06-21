<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\Api;

use PHPUnit\Framework\TestCase;
use TravelSorter\Api\Dispatcher;

class DispatcherTest extends TestCase
{
    /**
     * @dataProvider dispatchMethodProvider
     */
    public function testDispatchMethod(
        string $httpMethod,
        string $route,
        int $expectedStatusCode,
        bool $responseMustBeNull
    ) {
        $failMessage = sprintf('Testing method "%s" with route "%s".', $httpMethod, $route);
        $dispatcherResponse = (new Dispatcher($httpMethod))->dispatch($route);

        if ($responseMustBeNull) {
            $this->assertNull($dispatcherResponse, $failMessage);
        } else {
            $this->assertNotNull($dispatcherResponse, $failMessage);
            $this->assertEquals($expectedStatusCode, $dispatcherResponse->getStatusCode(), $failMessage);
        }
    }

    public function dispatchMethodProvider(): array
    {
        return [
            ['GET', '/api/sort', 404, false],
            ['GET', '/API/SORT', 404, true],
            ['GET', 'invalid-route', 404, true],
            ['GET', '', 404, true],
            ['POST', '/api/sort', 404, false],
            ['POST', '/API/SORT', 404, true],
            ['POST', 'invalid-route', 404, true],
            ['POST', '', 404, true],
            ['PUT', '/api/sort', 404, false],
            ['PUT', '/API/SORT', 404, true],
            ['PUT', 'invalid-route', 404, true],
            ['PUT', '', 404, true],
            ['DELETE', '/api/sort', 404, false],
            ['DELETE', '/API/SORT', 404, true],
            ['DELETE', 'invalid-route', 404, true],
            ['DELETE', '', 404, true],
            ['HEAD', '/api/sort', 404, false],
            ['HEAD', '/API/SORT', 404, true],
            ['HEAD', 'invalid-route', 404, true],
            ['HEAD', '', 404, true],
            ['CONNECT', '/api/sort', 404, false],
            ['CONNECT', '/API/SORT', 404, true],
            ['CONNECT', 'invalid-route', 404, true],
            ['CONNECT', '', 404, true],
            ['OPTIONS', '/api/sort', 404, false],
            ['OPTIONS', '/API/SORT', 404, true],
            ['OPTIONS', 'invalid-route', 404, true],
            ['OPTIONS', '', 404, true],
            ['TRACE', '/api/sort', 404, false],
            ['TRACE', '/API/SORT', 404, true],
            ['TRACE', 'invalid-route', 404, true],
            ['TRACE', '', 404, true],
            ['INVALID', '/api/sort', 404, false],
            ['INVALID', '/API/SORT', 404, true],
            ['INVALID', 'invalid-route', 404, true],
            ['INVALID', '', 404, true],
            ['get', '/api/sort', 404, false],
            ['get', '/API/SORT', 404, true],
            ['get', 'invalid-route', 404, true],
            ['get', '', 404, true],
            ['post', '/api/sort', 404, false],
            ['post', '/API/SORT', 404, true],
            ['post', 'invalid-route', 404, true],
            ['post', '', 404, true],
            ['put', '/api/sort', 404, false],
            ['put', '/API/SORT', 404, true],
            ['put', 'invalid-route', 404, true],
            ['put', '', 404, true],
            ['delete', '/api/sort', 404, false],
            ['delete', '/API/SORT', 404, true],
            ['delete', 'invalid-route', 404, true],
            ['delete', '', 404, true],
            ['head', '/api/sort', 404, false],
            ['head', '/API/SORT', 404, true],
            ['head', 'invalid-route', 404, true],
            ['head', '', 404, true],
            ['connect', '/api/sort', 404, false],
            ['connect', '/API/SORT', 404, true],
            ['connect', 'invalid-route', 404, true],
            ['connect', '', 404, true],
            ['options', '/api/sort', 404, false],
            ['options', '/API/SORT', 404, true],
            ['options', 'invalid-route', 404, true],
            ['options', '', 404, true],
            ['trace', '/api/sort', 404, false],
            ['trace', '/API/SORT', 404, true],
            ['trace', 'invalid-route', 404, true],
            ['trace', '', 404, true],
            ['invalid', '/api/sort', 404, false],
            ['invalid', '/API/SORT', 404, true],
            ['invalid', 'invalid-route', 404, true],
            ['invalid', '', 404, true],
            ['', '/api/sort', 404, false],
            ['', '/API/SORT', 404, true],
            ['', 'invalid-route', 404, true],
            ['', '', 404, true],
        ];
    }
}
