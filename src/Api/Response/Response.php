<?php
declare(strict_types=1);


namespace TravelSorter\Api\Response;

class Response implements ResponseInterface
{
    /**
     * @var ResponseBodyInterface
     */
    protected $body;
    /**
     * @var int
     */
    protected $statusCode;

    public function __construct(?ResponseBodyInterface $body, int $statusCode)
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
    }

    /**
     * @return ResponseBodyInterface
     */
    public function getBody(): ?ResponseBodyInterface
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}