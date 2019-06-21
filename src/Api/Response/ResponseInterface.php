<?php
declare(strict_types=1);


namespace TravelSorter\Api\Response;

interface ResponseInterface
{
    /**
     * The response body.
     *
     * @return ResponseBodyInterface
     */
    public function getBody(): ?ResponseBodyInterface;

    /**
     * Status code of the response.
     *
     * @return int
     */
    public function getStatusCode(): int;
}