<?php
declare(strict_types=1);

namespace TravelSorter\Test\Api\RequestHandler;

use PHPUnit\Framework\TestCase;
use TravelSorter\Api\RequestHandler\PostHandler;
use TravelSorter\Api\Response\ResponseBody\Error;
use TravelSorter\Api\Response\ResponseBody\ListOfTickets;
use TravelSorter\App\TicketsSorter\TicketsSorterInterface;

class PostHandlerTest extends TestCase
{
    public function testCanHandle()
    {
        $requestHandler = new PostHandler($this->createMock(TicketsSorterInterface::class));

        $this->assertTrue($requestHandler->canHandle('post'));
        $this->assertTrue($requestHandler->canHandle('POST'));
        $this->assertTrue($requestHandler->canHandle('PosT'));

        $this->assertFalse($requestHandler->canHandle('GET'));
        $this->assertFalse($requestHandler->canHandle('DELETE'));
        $this->assertFalse($requestHandler->canHandle('PUT'));
        $this->assertFalse($requestHandler->canHandle('HEAD'));
        $this->assertFalse($requestHandler->canHandle(''));
    }

    public function testSuccessfulRequest()
    {
        $requestBody = (object)[
            'tickets' => [
                (object)[
                    'transport' => 'Flight',
                    'origin' => 'Barcelona',
                    'destiny' => 'New York',
                ],
                (object)[
                    'transport' => 'Flight',
                    'origin' => 'New York',
                    'destiny' => 'Stockholm',
                    'seat' => '42F',
                    'gate' => '13',
                    'extra' => 'Baggage will we be automatically transferred.',
                ],
            ]
        ];

        $mockTicketsSorter = $this->createMock(TicketsSorterInterface::class);
        $mockTicketsSorter->method('sort')->willReturn([]);

        $requestHandler = new PostHandler($mockTicketsSorter);
        $response = $requestHandler->handleIt($requestBody);

        $this->assertEquals(200, $response->getStatusCode());

        /** @var ListOfTickets $responseBody */
        $responseBody = $response->getBody();
        $this->assertInstanceOf(ListOfTickets::class, $responseBody);

        $this->assertSame('{"tickets":[]}', $responseBody->toJson());
    }

    /**
     * @dataProvider requestWithIncompleteBodyProvider
     */
    public function testRequestWithIncompleteBody(
        string $expectedErrorMessage,
        int $expectedStatusCode,
        ?\stdClass $requestBody
    ) {
        $requestHandler = new PostHandler($this->createMock(TicketsSorterInterface::class));
        $response = $requestHandler->handleIt($requestBody);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());

        /** @var Error $responseBody */
        $responseBody = $response->getBody();
        $this->assertInstanceOf(Error::class, $responseBody);
        $this->assertStringContainsString($expectedErrorMessage, $responseBody->getDetail());
    }

    public function requestWithIncompleteBodyProvider(): array
    {
        return [
            ['Missing body content.', 422, null],
            ['Missing the "tickets" attribute.', 422, (object)[]],
            [
                'Please provide a value for the "transport" attribute.',
                422,
                (object)[
                    'tickets' => [
                        (object)[
                            'transport' => '',
                            'origin' => 'Barcelona',
                            'destiny' => 'New York',
                        ]
                    ]
                ]
            ],
            [
                'Please provide a value for the "origin" attribute.',
                422,
                (object)[
                    'tickets' => [
                        (object)[
                            'transport' => 'Flight',
                            'origin' => '',
                            'destiny' => 'New York',
                        ]
                    ]
                ]
            ],
            [
                'Please provide a value for the "destiny" attribute.',
                422,
                (object)[
                    'tickets' => [
                        (object)[
                            'transport' => 'Flight',
                            'origin' => 'Barcelona',
                            'destiny' => '',
                        ]
                    ]
                ]
            ],
        ];
    }
}
