<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\Api\Response;

use PHPUnit\Framework\TestCase;
use TravelSorter\Api\Response\Response;
use TravelSorter\Api\Response\ResponseBodyInterface;

class ResponseTest extends TestCase
{
    /**
     * @dataProvider getterMethodsProvider
     */
    public function testGetterMethods(int $statusCode, ?ResponseBodyInterface $body)
    {
        $response = new Response($body, $statusCode);

        $this->assertSame($body, $response->getBody());
        $this->assertSame($statusCode, $response->getStatusCode());
    }

    public function getterMethodsProvider(): array
    {
        return [
            [204, null],
            [201, $this->createMock(ResponseBodyInterface::class)],
        ];
    }
}
