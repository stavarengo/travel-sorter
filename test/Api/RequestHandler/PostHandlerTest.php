<?php
declare(strict_types=1);

namespace TravelSorter\Test\Api\RequestHandler;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use TravelSorter\Api\RequestHandler\PostHandler;
use TravelSorter\App\TicketsSorter\TicketsSorterInterface;

class PostHandlerTest extends TestCase
{
    public function testSuccessfulRequest()
    {
        $requestBody = json_encode([
            'tickets' => [
                [
                    'transport' => 'Flight',
                    'origin' => 'Barcelona',
                    'destiny' => 'New York',
                ],
                [
                    'transport' => 'Flight',
                    'origin' => 'New York',
                    'destiny' => 'Stockholm',
                    'seat' => '42F',
                    'gate' => '13',
                    'extra' => 'Baggage will we be automatically transferred.',
                ],
            ]
        ]);

        $mockTicketsSorter = $this->createMock(TicketsSorterInterface::class);
        $mockTicketsSorter->method('sort')->willReturn([]);

        $requestHandler = new PostHandler($mockTicketsSorter);
        $response = $requestHandler->handleIt($this->mockRequest($requestBody));

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertSame('{"tickets":[]}', $response->getBody()->getContents());
    }

    private function mockRequest(?string $bodyContent): RequestInterface
    {
        $mockStream = $this->createMock(StreamInterface::class);
        $mockStream->method('getContents')->willReturn($bodyContent);

        $mockRequest = $this->createMock(RequestInterface::class);
        $mockRequest->method('getBody')->willReturn($mockStream);

        return $mockRequest;
    }

    /**
     * @dataProvider requestWithIncompleteBodyProvider
     */
    public function testRequestWithIncompleteBody(
        string $expectedErrorMessage,
        int $expectedStatusCode,
        RequestInterface $request
    ) {
        $requestHandler = new PostHandler($this->createMock(TicketsSorterInterface::class));
        $response = $requestHandler->handleIt($request);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());

        $this->assertStringContainsString($expectedErrorMessage, $response->getBody()->getContents());
    }

    public function requestWithIncompleteBodyProvider(): array
    {
        return [
            [json_encode('Missing body content.'), 422, $this->mockRequest(null)],
            [json_encode('Missing body content.'), 422, $this->mockRequest('')],
            [json_encode('Missing the "tickets" attribute.'), 422, $this->mockRequest('[]')],
            [json_encode('Missing the "tickets" attribute.'), 422, $this->mockRequest('{}')],
            [
                trim(json_encode('Please provide a value for the "transport" attribute.'), '"'),
                422,
                $this->mockRequest(json_encode([
                    'tickets' => [
                        [
                            'transport' => '',
                            'origin' => 'Barcelona',
                            'destiny' => 'New York',
                        ]
                    ]
                ]))
            ],
            [
                trim(json_encode('Please provide a value for the "origin" attribute.'), '"'),
                422,
                $this->mockRequest(json_encode([
                    'tickets' => [
                        [
                            'transport' => 'Flight',
                            'origin' => '',
                            'destiny' => 'New York',
                        ]
                    ]
                ]))
            ],
            [
                trim(json_encode('Please provide a value for the "destiny" attribute.'), '"'),
                422,
                $this->mockRequest(json_encode([
                    'tickets' => [
                        [
                            'transport' => 'Flight',
                            'origin' => 'Barcelona',
                            'destiny' => '',
                        ]
                    ]
                ]))
            ],
        ];
    }

}
