<?php
declare(strict_types=1);


namespace TravelSorter\App\Dispatcher;


interface DispatcherInterface
{
    /**
     * Dispatch the request based on the $requestRoute.
     * It should return a response if, and only if, the $requestRoute is one of its route.
     * If the dispatch does not support the $requestRoute it should return null;
     *
     * @param string $requestRoute
     *      The route requested.
     *
     * @param string $httpMethod
     *      The HTTP method of the request.
     *      Eg: POST, PUT, GET, HEAD, etc.
     *
     * @return DispatcherResponse|null
     *
     */
    public function dispatch(string $requestRoute, string $httpMethod): ?DispatcherResponse;
}