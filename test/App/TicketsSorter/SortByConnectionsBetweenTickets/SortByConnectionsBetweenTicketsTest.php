<?php /** @noinspection PhpDocMissingThrowsInspection */
declare(strict_types=1);

namespace TravelSorter\Test\App\TicketsSorter\SortByConnectionsBetweenTickets;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TravelSorter\App\TicketsSorter\SortByConnectionsBetweenTickets\Exception\MissingTicketsConnection;
use TravelSorter\App\TicketsSorter\SortByConnectionsBetweenTickets\Exception\YourTripEndsWhereItStarted;
use TravelSorter\App\TicketsSorter\SortByConnectionsBetweenTickets\SortByConnectionsBetweenTickets;
use TravelSorter\App\TicketsSorter\TicketInterface;

class SortByConnectionsBetweenTicketsTest extends TestCase
{
    /**
     * @param TicketInterface[] $ticketsInWrongOrder
     * @dataProvider sortingTicketsCorrectlyProvider
     */
    public function testSortingTicketsCorrectly(array $ticketsInWrongOrder)
    {
        $ticketsSorter = new SortByConnectionsBetweenTickets();

        $ticketsInTheExpectedOrder = $ticketsInWrongOrder;
        ksort($ticketsInTheExpectedOrder);

        $ticketsSorted = $ticketsSorter->sort($ticketsInWrongOrder);

        $this->assertCount(
            count($ticketsInTheExpectedOrder),
            $ticketsSorted,
            'The sorted tickets has a different amount of tickets.'
        );

        $tickedPosition = 0;
        foreach ($ticketsInTheExpectedOrder as $expectedTicket) {
            $actualTicket = array_shift($ticketsSorted);

            $this->assertSame(
                $expectedTicket,
                $actualTicket,
                sprintf(
                    'Found a wrong ticked in the position "%s". Expect a ticket for a trip between "%s => %s", found "%s => %s".',
                    $tickedPosition,
                    $expectedTicket->getOrigin(),
                    $expectedTicket->getDestiny(),
                    $actualTicket->getOrigin(),
                    $actualTicket->getDestiny()
                )
            );

            $tickedPosition++;
        }
    }

    public function sortingTicketsCorrectlyProvider(): array
    {
        // This dataset contains a list of tickets in the wrong order, but the key of each ticket tell
        // what is the expected position after sorted.
        return [
            [[]],
            [
                [
                    0 => $this->mockTicket('A', 'B'),
                ]
            ],
            [
                [
                    0 => $this->mockTicket('A', 'B'),
                    1 => $this->mockTicket('B', 'C'),
                ]
            ],
            [
                [
                    1 => $this->mockTicket('B', 'C'),
                    0 => $this->mockTicket('A', 'B'),
                ]
            ],
            [
                // This covers the case-sensitive problem
                [
                    1 => $this->mockTicket('Báãü', 'C'),
                    0 => $this->mockTicket('A', 'bÁÃÜ'),
                ]
            ],
            [
                // This covers when there is an EVEN number tickets in RANDOMIZED order
                [
                    1 => $this->mockTicket('Barcelona', 'Gerona Airport'),
                    0 => $this->mockTicket('Madrid', 'Barcelona'),
                    3 => $this->mockTicket('Stockholm', 'New York JFK'),
                    2 => $this->mockTicket('Gerona Airport', 'Stockholm'),
                ],
            ],
            [
                // This covers when there is an EVEN number tickets in REVERSE order
                [
                    3 => $this->mockTicket('Stockholm', 'New York JFK'),
                    2 => $this->mockTicket('Gerona Airport', 'Stockholm'),
                    1 => $this->mockTicket('Barcelona', 'Gerona Airport'),
                    0 => $this->mockTicket('Madrid', 'Barcelona'),
                ],
            ],
            [
                // This covers when there is an ODD number tickets in RANDOMIZED order
                [
                    1 => $this->mockTicket('Barcelona', 'Gerona Airport'),
                    0 => $this->mockTicket('Madrid', 'Barcelona'),
                    2 => $this->mockTicket('Gerona Airport', 'Stockholm'),
                ],
            ],
            [
                // This covers when there is an ODD number tickets in REVERSE order
                [
                    2 => $this->mockTicket('Gerona Airport', 'Stockholm'),
                    1 => $this->mockTicket('Barcelona', 'Gerona Airport'),
                    0 => $this->mockTicket('Madrid', 'Barcelona'),
                ],
            ],
        ];
    }

    /**
     * @param TicketInterface[] $tickets
     * @dataProvider tryToSortWhenTheFinalDestinyIsEqualsToTheBeginProvider
     */
    public function testTryToSortWhenTheFinalDestinyIsEqualsToTheBegin(array $tickets)
    {
        $ticketsSorter = new SortByConnectionsBetweenTickets();

        $this->expectException(YourTripEndsWhereItStarted::class);
        $ticketsSorter->sort($tickets);
    }

    public function tryToSortWhenTheFinalDestinyIsEqualsToTheBeginProvider(): array
    {
        return [
            [
                [
                    $this->mockTicket('Madrid', 'Barcelona'),
                    $this->mockTicket('Barcelona', 'Madrid'),
                ],
            ],
            [
                [
                    $this->mockTicket('Madrid', 'Barcelona'),
                    $this->mockTicket('Barcelona', 'New York JFK'),
                    $this->mockTicket('New York JFK', 'Madrid'),
                ],
            ],
        ];
    }

    /**
     * @param TicketInterface[] $tickets
     * @dataProvider tryToSortWhenThereIsAMissingConnectionProvider
     */
    public function testTryToSortWhenThereIsAMissingConnection(array $tickets)
    {
        $ticketsSorter = new SortByConnectionsBetweenTickets();

        $this->expectException(MissingTicketsConnection::class);
        $ticketsSorter->sort($tickets);
    }

    public function tryToSortWhenThereIsAMissingConnectionProvider(): array
    {
        return [
            [
                [
                    $this->mockTicket('Madrid', 'Barcelona'),
                    $this->mockTicket('New York JFK', 'Stockholm'),
                ],
            ],
            [
                [
                    $this->mockTicket('Madrid', 'Barcelona'),
                    $this->mockTicket('Madrid', 'Stockholm'),
                ],
            ],
            [
                [
                    $this->mockTicket('Madrid', 'Barcelona'),
                    $this->mockTicket('Barcelona', 'Florence'),
                    $this->mockTicket('New York JFK', 'Stockholm'),
                ],
            ],

            [
                // There is a connection missing and one of the tickets STARTS in the same place of another ticket
                [
                    $this->mockTicket('Barcelona', 'Madrid'),
                    $this->mockTicket('Barcelona', 'New York JFK'),
                ],
            ],
            [
                // There is a connection missing and one of the tickets STARTS in the same place of another ticket
                [
                    $this->mockTicket('Barcelona', 'Madrid'),
                    $this->mockTicket('Madrid', 'Stockholm'),
                    $this->mockTicket('Barcelona', 'New York JFK'),
                ],
            ],

            [
                // There is a connection missing and one of the tickets ENDS in the same place of another ticket
                [
                    $this->mockTicket('Barcelona', 'Madrid'),
                    $this->mockTicket('New York JFK', 'Madrid'),
                ],
            ],
            [
                // There is a connection missing and one of the tickets ENDS in the same place of another ticket
                [
                    $this->mockTicket('Barcelona', 'Madrid'),
                    $this->mockTicket('Madrid', 'Stockholm'),
                    $this->mockTicket('New York JFK', 'Stockholm'),
                ],
            ],
        ];
    }

    private function mockTicket(string $origin, string $destiny): TicketInterface
    {
        /** @var TicketInterface|MockObject $mockTicket */
        $mockTicket = $this->createMock(TicketInterface::class);

        $mockTicket->method('getOrigin')->willReturn($origin);
        $mockTicket->method('getDestiny')->willReturn($destiny);

        return $mockTicket;
    }
}
