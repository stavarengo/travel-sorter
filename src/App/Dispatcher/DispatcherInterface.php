<?php
declare(strict_types=1);


namespace TravelSorter\App\Dispatcher;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface DispatcherInterface
{
    /**
     * This must be an array with the following format:
     *  [
     *      'ROUTE_NAME' => [
     *          'REQUEST_METHOD' => 'REQUEST_HANDLER_SERVICE_NAME'
     *      ]
     *  ]
     */
    public const REQUEST_HANDLER_MAP = 'dispatchers';

    /**
     * Dispatch the request based on the $requestRoute.
     * It should return a response if, and only if, the $requestRoute is one of its route.
     * If the dispatch does not support the $requestRoute it should return null;
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface|null
     */
    public function dispatch(RequestInterface $request): ?ResponseInterface;
}