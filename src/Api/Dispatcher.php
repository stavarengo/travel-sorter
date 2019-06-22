<?php
declare(strict_types=1);


namespace TravelSorter\Api;


use TravelSorter\Api\RequestHandler\PostHandler;
use TravelSorter\Api\RequestHandler\RequestHandlerInterface;
use TravelSorter\Api\Response\Response;
use TravelSorter\Api\Response\ResponseBody\Error;
use TravelSorter\App\Dispatcher\DispatcherInterface;
use TravelSorter\App\Dispatcher\DispatcherResponse;

class Dispatcher implements DispatcherInterface
{
    /**
     * @var PostHandler[]
     */
    private $handlers;

    /**
     * Dispatcher constructor.
     * @param RequestHandlerInterface[] $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    public function dispatch(string $requestRoute, string $httpMethod): ?DispatcherResponse
    {
        if ($requestRoute != '/api/sort') {
            // This route does not belongs to this dispatcher.
            return null;
        }

        $apiResponse = null;
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($httpMethod)) {
                $requestBody = null;
                if (strtoupper($httpMethod) === 'POST') {
                    $input = file_get_contents('php://input');
                    $requestBody = json_decode($input);
                }
                $apiResponse = $handler->handleIt($requestBody);
                break;
            }
        }

        if (!$apiResponse) {
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

    /**
     * @return PostHandler[]
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }
}