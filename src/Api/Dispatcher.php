<?php
declare(strict_types=1);


namespace TravelSorter\Api;


use TravelSorter\Api\RequestHandler\RequestHandlerInterface;
use TravelSorter\Api\Response\Response;
use TravelSorter\Api\Response\ResponseBody\Error;
use TravelSorter\App\Dispatcher\DispatcherInterface;
use TravelSorter\App\Dispatcher\DispatcherResponse;

class Dispatcher implements DispatcherInterface
{
    public function dispatch(string $requestRoute, string $httpMethod): ?DispatcherResponse
    {
        if ($requestRoute != '/api/sort') {
            // This route does not belongs to this dispatcher.
            return null;
        }

        $requestBody = null;
        switch (strtoupper($httpMethod)) {
            default:
                /** @var RequestHandlerInterface $handler */
                $handler = null;
        }

        if ($handler) {
            $apiResponse = $handler->handleIt($requestBody);
        } else {
            $apiResponse = new Response(new Error('Not found'), 404);
        }


        return new DispatcherResponse(
            $apiResponse->getStatusCode(),
            $apiResponse->getBody() ? $apiResponse->getBody()->toJson() : '',
            [
                'Content-Type' => 'application/json; charset=UTF-8',
            ]
        );
    }
}