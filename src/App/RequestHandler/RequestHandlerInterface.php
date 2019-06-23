<?php
declare(strict_types=1);


namespace TravelSorter\App\RequestHandler;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestHandlerInterface
{
    /**
     * Handle the received request.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handleIt(RequestInterface $request): ResponseInterface;
}