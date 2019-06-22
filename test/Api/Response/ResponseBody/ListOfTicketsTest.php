<?php
declare(strict_types=1);

namespace TravelSorter\Test\Api\Response\ResponseBody;

use PHPUnit\Framework\TestCase;
use TravelSorter\Api\Response\ResponseBody\ListOfTickets;
use TravelSorter\App\TicketsSorter\TicketInterface;

class ListOfTicketsTest extends TestCase
{
    /**
     * @dataProvider ListOfTicketsProvider
     */
    public function testListOfTickets(array $ticketsAsArray)
    {
        $tickets = array_map(function (array $ticketAsArray) {
            return $this->mockTicket(
                $ticketAsArray['transport'],
                $ticketAsArray['origin'],
                $ticketAsArray['destiny'],
                $ticketAsArray['seat'],
                $ticketAsArray['gate'],
                $ticketAsArray['extra']
            );
        }, $ticketsAsArray);

        $listOfTickets = new ListOfTickets($tickets);

        $this->assertCount(count($tickets), $listOfTickets->getTickets());

        $this->assertJsonStringEqualsJsonString(json_encode(['tickets' => array_values($ticketsAsArray)]), $listOfTickets->toJson());
    }

    private function mockTicket(
        ?string $transport,
        ?string $origin,
        ?string $destiny,
        ?string $seat,
        ?string $gate,
        ?string $extra
    ): TicketInterface {
        $mockTicket = $this->createMock(TicketInterface::class);
        $mockTicket->method('getTransport')->willReturn($transport);
        $mockTicket->method('getOrigin')->willReturn($origin);
        $mockTicket->method('getDestiny')->willReturn($destiny);
        $mockTicket->method('getSeat')->willReturn($seat);
        $mockTicket->method('getGate')->willReturn($gate);
        $mockTicket->method('getExtra')->willReturn($extra);

        return $mockTicket;
    }

    public function ListOfTicketsProvider(): array
    {
        return [
            [
                [
                    1 => [
                        'transport' => 'Flight',
                        'origin' => 'New York',
                        'destiny' => 'Stockholm',
                        'seat' => '42F',
                        'gate' => '13',
                        'extra' => 'Baggage drop at ticket counter 344.'
                    ]
                ]
            ],
            [
                [
                    1 => [
                        'transport' => 'Train',
                        'origin' => 'Rome',
                        'destiny' => 'Florence',
                        'seat' => null,
                        'gate' => null,
                        'extra' => null
                    ],
                    0 => [
                        'transport' => 'Airport bus',
                        'origin' => 'Barcelona',
                        'destiny' => 'Gerona Airport',
                        'seat' => null,
                        'gate' => 'D',
                        'extra' => 'Baggage will we be automatically transferred.'
                    ]
                ]
            ],
        ];
    }
}
