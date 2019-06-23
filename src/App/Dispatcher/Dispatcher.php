<?php
declare(strict_types=1);


namespace TravelSorter\App\Dispatcher;


use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TravelSorter\Api\ResponseBody\Error;
use TravelSorter\App\RouteDetector\RouteDetectorInterface;
use function GuzzleHttp\Psr7\stream_for;

class Dispatcher implements DispatcherInterface
{
    /**
     * @var array[]
     */
    protected $requestHandlerMap;
    /**
     * @var RouteDetectorInterface
     */
    private $routeDetector;

    /**
     * DispatcherAggregate constructor.
     * @param RouteDetectorInterface $routeDetector
     * @param array[] $requestHandlerMap
     */
    public function __construct(
        RouteDetectorInterface $routeDetector,
        array $requestHandlerMap
    ) {
        $this->requestHandlerMap = $requestHandlerMap;
        $this->routeDetector = $routeDetector;
    }

    public function dispatch(RequestInterface $request): ?ResponseInterface
    {
        $requestRoute = $this->routeDetector->detect($request->getUri());
        $requestHandlerMap = $this->requestHandlerMap[$requestRoute] ?? null;

        if (!$requestHandlerMap) {
            return null;
        }

        /** @var \TravelSorter\App\RequestHandler\RequestHandlerInterface $requestHandler */
        $requestMethod = strtoupper($request->getMethod());
        $requestHandler = $requestHandlerMap[$requestMethod] ?? null;

        $response = null;
        if ($requestHandler) {
            $response = $requestHandler->handleIt($request);
        } else {
            $allowedMethods = implode(', ', array_keys($requestHandlerMap));
            $headers = ['Allowed' => $allowedMethods];
            $response = new Response(
                405,
                $headers,
                stream_for(new Error(sprintf('Method not allowed. Use one of the following: %s.', $allowedMethods)))
            );
        }

        return $response;
    }

    /**
     * @return RouteDetectorInterface
     */
    public function getRouteDetector(): RouteDetectorInterface
    {
        return $this->routeDetector;
    }

    /**
     * @return DispatcherInterface[]
     */
    public function getRequestHandlerMap(): array
    {
        return $this->requestHandlerMap;
    }
}