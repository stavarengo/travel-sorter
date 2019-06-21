<?php
declare(strict_types=1);


namespace TravelSorter\App\Dispatcher;


interface DispatcherInterface
{
    /**
     * All dispatchers of the application.
     */
    public const CONFIG_DISPATCHERS = 'dispatchers';

    /**
     * Dispatch the request based on the $requestRoute.
     * It should return a response if, and only if, the $requestRoute is one of its route.
     * If the dispatch does not support the $requestRoute it should return null;
     *
     * @param string $requestRoute
     *      The route requested.
     *
     * @return DispatcherResponse|null
     *
     * @throws \Throwable
     *      It can throw any exception during execution.
     */
    public function dispatch(string $requestRoute): ?DispatcherResponse;
}