<?php
declare(strict_types=1);


namespace TravelSorter\Api\RequestHandler;


use TravelSorter\Api\Response\ResponseInterface;

interface RequestHandlerInterface
{
    /**
     * Returns true if this handler knows how to handle the request.
     *
     * @param string $httpMethod
     *      The HTTP request method used in the request.
     *
     * @return bool
     */
    public function canHandle(string $httpMethod): bool;

    /**
     * Handle the received request.
     *
     * @param \stdClass|null $requestBody
     *      The request body, if any.
     *
     * @return ResponseInterface
     */
    public function handleIt(?\stdClass $requestBody): ResponseInterface;
}