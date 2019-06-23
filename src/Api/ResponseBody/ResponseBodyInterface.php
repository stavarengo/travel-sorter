<?php
declare(strict_types=1);


namespace TravelSorter\Api\ResponseBody;


interface ResponseBodyInterface
{
    /**
     * Convert the response to its JSON representation.
     *
     * @return string
     */
    public function __toString(): string;
}