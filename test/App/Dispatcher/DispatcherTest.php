<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\App\Dispatcher;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use TravelSorter\App\Dispatcher\Dispatcher;
use TravelSorter\App\RequestHandler\RequestHandlerInterface;
use TravelSorter\App\RouteDetector\RouteDetectorInterface;

class DispatcherTest extends TestCase
{
    public function testWithNoDispatchers()
    {
        $dispatcher = new Dispatcher($this->mockRouteDetector('/'), []);
        $this->assertNull($dispatcher->dispatch($this->mockRequest('GET')));
    }

    private function mockRouteDetector(string $routeName): RouteDetectorInterface
    {
        $stubRouteDetector = $this->createMock(RouteDetectorInterface::class);

        $stubRouteDetector->method('detect')->willReturn($routeName);

        return $stubRouteDetector;
    }

    private function mockRequest(string $httpMethod): RequestInterface
    {
        $mockRequest = $this->createMock(RequestInterface::class);
        $mockRequest->method('getMethod')->willReturn($httpMethod);
        $mockRequest->method('getUri')->willReturn($this->createMock(UriInterface::class));

        return $mockRequest;
    }

    public function testWithRequestHandlersThatReturnNonNullValues()
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $stubRequestHandler = $this->createMock(RequestHandlerInterface::class);
        $stubRequestHandler->method('handleIt')->willReturn($expectedResponse);

        $routeName = 'route1';
        $httpMethod = 'GET';
        $requestHandlerMap = [
            $routeName => [
                $httpMethod => $stubRequestHandler,
            ],
        ];

        $dispatcher = new Dispatcher($this->mockRouteDetector($routeName), $requestHandlerMap);
        $this->assertSame($expectedResponse, $dispatcher->dispatch($this->mockRequest($httpMethod)));
    }

    public function testRequestWithMethodNotAllowed()
    {
        $routeName = 'route1';
        $methodMap = [
            'GET' => $this->createMock(RequestHandlerInterface::class),
            'POST' => $this->createMock(RequestHandlerInterface::class),
        ];
        $requestHandlerMap = [
            $routeName => $methodMap,
        ];

        $notAllowedMethod = 'PUT';
        $this->assertArrayNotHasKey($notAllowedMethod, $methodMap);

        $dispatcher = new Dispatcher($this->mockRouteDetector($routeName), $requestHandlerMap);
        $response = $dispatcher->dispatch($this->mockRequest($notAllowedMethod));

        $this->assertNotNull($response);
        $this->assertSame(405, $response->getStatusCode());
        $this->assertArrayHasKey('Allowed', $response->getHeaders());
        $this->assertSame(implode(', ', array_keys($methodMap)), $response->getHeaderLine('Allowed'));
    }
}
