<?php
declare(strict_types=1);


namespace TravelSorter\Api\Response\ResponseBody;


use TravelSorter\Api\Response\ResponseBodyInterface;

class Error implements ResponseBodyInterface
{
    /**
     * @var string
     */
    protected $detail;

    /**
     * Error constructor.
     * @param string $detail
     */
    public function __construct(string $detail)
    {
        $this->detail = $detail;
    }

    /**
     * @return string
     */
    public function getDetail(): string
    {
        return $this->detail;
    }

    public function toJson(): string
    {
        $errorAsArray = [
            'error' => true,
            'detail' => $this->detail,
        ];

        return json_encode($errorAsArray);
    }
}