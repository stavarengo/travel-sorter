<?php
declare(strict_types=1);

namespace TravelSorter\Test\App\TicketsSorter;

use PHPUnit\Framework\TestCase;
use TravelSorter\App\TicketsSorter\Ticket;

class TicketTest extends TestCase
{
    /**
     * @dataProvider sortByTicketConnectionsProvider
     */
    public function testSortTicketsThatComeInReverseOrder(
        string $transport,
        string $origin,
        string $destiny,
        ?string $seat,
        ?string $gate,
        ?string $extra
    ) {
        $ticket = (new Ticket())
            ->setTransport($transport)
            ->setOrigin($origin)
            ->setDestiny($destiny)
            ->setSeat($seat)
            ->setGate($gate)
            ->setExtra($extra);

        $this->assertSame($transport, $ticket->getTransport());
        $this->assertSame($origin, $ticket->getOrigin());
        $this->assertSame($destiny, $ticket->getDestiny());
        $this->assertSame($seat, $ticket->getSeat());
        $this->assertSame($gate, $ticket->getGate());
        $this->assertSame($extra, $ticket->getExtra());
    }

    public function sortByTicketConnectionsProvider(): array
    {
        return [
            ['Flight', 'Barcelona', 'Madrid', '45B', '19', 'A driver will be waiting for you in Barcelona.'],
            ['Train', 'Rome', 'Florence', null, null, null],
        ];
    }
}
