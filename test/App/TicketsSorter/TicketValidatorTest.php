<?php
declare(strict_types=1);

namespace TravelSorter\Test\App\TicketsSorter;

use PHPUnit\Framework\TestCase;
use TravelSorter\App\TicketsSorter\TicketInterface;
use TravelSorter\App\TicketsSorter\TicketValidator;

class TicketValidatorTest extends TestCase
{
    /**
     * @dataProvider validateMethodProvider
     */
    public function testValidateMethod(?string $expectedErrorMessage, TicketInterface $ticket)
    {
        $ticketValidator = new TicketValidator();

        $validateResult = $ticketValidator->validate($ticket);

        $this->assertSame($expectedErrorMessage, $validateResult);
    }

    public function validateMethodProvider(): array
    {
        return [
            [
                null,
                $this->mockTicket('Train', 'Rome', 'Florence', '3F', '19', 'Baggage drop at ticket counter 344.')
            ],
            [
                null,
                $this->mockTicket('Train', 'Rome', 'Florence', '', '', '')
            ],
            [
                null,
                $this->mockTicket('Train', 'Rome', 'Florence')
            ],
            [
                'Missing value for the "transport" attribute.',
                $this->mockTicket()
            ],
            [
                'Missing value for the "transport" attribute.',
                $this->mockTicket('', 'Barcelona', 'New York')
            ],
            [
                'Missing value for the "transport" attribute.',
                $this->mockTicket('   ', 'Barcelona', 'New York')
            ],
            [
                'Missing value for the "transport" attribute.',
                $this->mockTicket(null, 'Barcelona', 'New York')
            ],
            [
                'Missing value for the "origin" attribute.',
                $this->mockTicket('Flight', '', 'New York')
            ],
            [
                'Missing value for the "origin" attribute.',
                $this->mockTicket('Flight', '   ', 'New York')
            ],
            [
                'Missing value for the "origin" attribute.',
                $this->mockTicket('Flight', null, 'New York')
            ],
            [
                'Missing value for the "destiny" attribute.',
                $this->mockTicket('Flight', 'Barcelona', '')
            ],
            [
                'Missing value for the "destiny" attribute.',
                $this->mockTicket('Flight', 'Barcelona', '  ')
            ],
            [
                'Missing value for the "destiny" attribute.',
                $this->mockTicket('Flight', 'Barcelona', null)
            ],
        ];
    }

    private function mockTicket(
        ?string $transport = null,
        ?string $origin = null,
        ?string $destiny = null,
        ?string $seat = null,
        ?string $gate = null,
        ?string $extra = null
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

}
