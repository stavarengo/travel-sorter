<?php
declare(strict_types=1);

namespace TravelSorter\Test\Api\Response\ResponseBody;

use PHPUnit\Framework\TestCase;
use TravelSorter\Api\Response\ResponseBody\Error;
use TravelSorter\Api\Response\ResponseBody\ListOfTickets;
use TravelSorter\App\TicketsSorter\TicketInterface;

class ListOfTicketsTest extends TestCase
{
    /**
     * @dataProvider ListOfTicketsProvider
     */
    public function testListOfTickets(array $tickets)
    {
        $listOfTickets = new ListOfTickets($tickets);

        $this->assertCount(count($tickets), $listOfTickets->getTickets());
    }

    public function ListOfTicketsProvider(): array
    {
        return [
            [
                [
                    $this->createMock(TicketInterface::class),
                ]
            ],
            [
                [
                    $this->createMock(TicketInterface::class),
                    $this->createMock(TicketInterface::class),
                ]
            ],
        ];
    }
}
