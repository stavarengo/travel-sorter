<?php
declare(strict_types=1);

namespace TravelSorter\Test\Api\Response\ResponseBody;

use PHPUnit\Framework\TestCase;
use TravelSorter\Api\Response\ResponseBody\Error;

class ErrorTest extends TestCase
{
    /**
     * @dataProvider errorProvider
     */
    public function testError(string $detail)
    {
        $error = new Error($detail);

        $this->assertEquals($detail, $error->getDetail());
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => true, 'detail' => $detail]), $error->toJson());
    }

    public function errorProvider(): array
    {
        return [
            ['Error detail 1'],
            ['Error detail 2'],
        ];
    }
}
