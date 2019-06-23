<?php
declare(strict_types=1);

namespace TravelSorter\Test\Api\RequestHandler;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use TravelSorter\Api\RequestHandler\PostHandler;
use TravelSorter\App\TicketsSorter\Exception\MissingTicketsConnection;
use TravelSorter\App\TicketsSorter\Exception\TicketsSorterException;
use TravelSorter\App\TicketsSorter\Exception\YourTripEndsWhereItStarted;
use TravelSorter\App\TicketsSorter\TicketsSorterInterface;
use TravelSorter\App\TicketsSorter\TicketValidatorInterface;

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

        $requestHandler = new PostHandler($mockTicketsSorter, $this->createMock(TicketValidatorInterface::class));
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
        $requestHandler = new PostHandler(
            $this->createMock(TicketsSorterInterface::class),
            $this->createMock(TicketValidatorInterface::class)
        );
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
        ];
    }

    /**
     * @param string $requestBodyContent
     * @param int $indexOfTheInvalidTicket
     * @param array $validateResultOnConsecutiveCalls
     *
     * @dataProvider validatorRejectsOneOfTheTicketsProvider
     */
    public function testValidatorRejectsOneOfTheTickets(
        string $requestBodyContent,
        int $indexOfTheInvalidTicket,
        array $validateResultOnConsecutiveCalls
    ) {
        $mockTicketValidator = $this->mockTicketValidator($validateResultOnConsecutiveCalls);

        $requestHandler = new PostHandler($this->createMock(TicketsSorterInterface::class), $mockTicketValidator);

        $response = $requestHandler->handleIt($this->mockRequest($requestBodyContent));

        $this->assertEquals(422, $response->getStatusCode());

        $responseContent = $response->getBody()->getContents();

        $this->assertStringContainsString(
            sprintf('There is a problem with the ticket you put in the position \"%s\".', $indexOfTheInvalidTicket),
            $responseContent
        );
        $this->assertStringContainsString(
            $validateResultOnConsecutiveCalls[$indexOfTheInvalidTicket],
            $responseContent
        );
    }

    private function mockTicketValidator(array $validateResultOnConsecutiveCalls): TicketValidatorInterface
    {
        $mockTicketValidator = $this->createMock(TicketValidatorInterface::class);
        $mockTicketValidator->method('validate')->willReturnOnConsecutiveCalls(...$validateResultOnConsecutiveCalls);

        return $mockTicketValidator;
    }

    public function validatorRejectsOneOfTheTicketsProvider(): array
    {
        return [
            ['{"tickets": [{}]}', 0, ['Missing attribute.']],
            ['{"tickets": [{}, {}]}', 1, [null, 'Missing attribute on second ticket.']],
        ];
    }

    /**
     * @param int $expectedStatusCode
     * @param string $expectedResponseMessage
     * @param \Exception $ticketSorterException
     *
     * @dataProvider ticketSorterThrowsAndExceptionProvider
     */
    public function testTicketSorterThrowsAndException(
        int $expectedStatusCode,
        string $expectedResponseMessage,
        \Exception $ticketSorterException
    ) {
        $mockTicketsSorter = $this->createMock(TicketsSorterInterface::class);
        $mockTicketsSorter->method('sort')->willThrowException($ticketSorterException);

        $requestHandler = new PostHandler($mockTicketsSorter, $this->mockTicketValidator([null]));
        $response = $requestHandler->handleIt($this->mockRequest('{"tickets": [{}]}'));
        $responseContent = $response->getBody()->getContents();

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
        $this->assertStringContainsString($expectedResponseMessage, $responseContent);
    }

    public function ticketSorterThrowsAndExceptionProvider(): array
    {
        return [
            [400, ($msg = 'Missing connections.'), new MissingTicketsConnection($msg)],
            [400, ($msg = 'End where starts.'), new YourTripEndsWhereItStarted($msg)],
            [400, ($msg = 'Another exception.'), new TicketsSorterException($msg)],
            [500, 'Unexpected error while trying to sort your tickets.', new \RuntimeException('Runtime error.')],
        ];
    }
}
