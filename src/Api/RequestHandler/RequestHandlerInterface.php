<?php
declare(strict_types=1);


namespace TravelSorter\Api\RequestHandler;


use TravelSorter\Api\Response\ResponseInterface;

interface RequestHandlerInterface
{
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