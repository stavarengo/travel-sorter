<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\Api;

use PHPUnit\Framework\TestCase;
use TravelSorter\Api\Dispatcher;
use TravelSorter\Api\RequestHandler\RequestHandlerInterface;
use TravelSorter\Api\Response\ResponseInterface;

class DispatcherTest extends TestCase
{
    public function testRequestWithWrongRoute()
    {
        $dispatcherResponse = (new Dispatcher([]))->dispatch('invalid_route', 'GET');

        $this->assertNull($dispatcherResponse);
    }

    /**
     * @dataProvider dispatchMustUseTheRightRequestHandlerProvider
     */
    public function testDispatchMustUseTheRightRequestHandler(string $httpMethod, int $expectedStatusCode, array $handlers)
    {
        $this->assertNull((new Dispatcher($handlers))->dispatch('invalid_route_with_handlers', $httpMethod));

        $dispatcherResponse = (new Dispatcher($handlers))->dispatch('/api/sort', $httpMethod);

        $this->assertNotNull($dispatcherResponse);
        $this->assertEquals($expectedStatusCode, $dispatcherResponse->getStatusCode());
    }

    public function dispatchMustUseTheRightRequestHandlerProvider(): array
    {
        return [
            ['GET', 404, []],
            ['GET', 404, [$this->mockRequestHandler(false)]],
            ['GET', 200, [$this->mockRequestHandler(false), $this->mockRequestHandler(true, 200)]],
            [
                'POST',
                201,
                [
                    $this->mockRequestHandler(false),
                    $this->mockRequestHandler(true, 201),
                    $this->mockRequestHandler(true, 200)
                ]
            ],
        ];
    }

    private function mockRequestHandler(bool $canHandle, ?int $statusCode = null): RequestHandlerInterface
    {
        $mockRequestHandler = $this->createMock(RequestHandlerInterface::class);
        $mockRequestHandler->method('canHandle')->willReturn($canHandle);

        if ($canHandle) {
            $mockResponse = $this->createMock(ResponseInterface::class);
            $mockResponse->method('getStatusCode')->willReturn($statusCode);

            $mockRequestHandler->method('handleIt')->willReturn($mockResponse);
        }

        return $mockRequestHandler;
    }

}
