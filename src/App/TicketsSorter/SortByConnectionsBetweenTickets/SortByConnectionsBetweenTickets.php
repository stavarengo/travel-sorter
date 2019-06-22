<?php
declare(strict_types=1);


namespace TravelSorter\App\TicketsSorter\SortByConnectionsBetweenTickets;


use TravelSorter\App\TicketsSorter\SortByConnectionsBetweenTickets\Exception\MissingTicketsConnection;
use TravelSorter\App\TicketsSorter\SortByConnectionsBetweenTickets\Exception\YourTripEndsWhereItStarted;
use TravelSorter\App\TicketsSorter\TicketInterface;
use TravelSorter\App\TicketsSorter\TicketsSorterInterface;

class SortByConnectionsBetweenTickets implements TicketsSorterInterface
{
    /**
     * Sorts the $tickets.
     *
     * @param TicketInterface[] $tickets
     *  The list of {@link Ticket} to be sorted.
     *
     * @return TicketInterface[]
     *  The sorted list of {@link Ticked}
     * @throws MissingTicketsConnection
     * @throws YourTripEndsWhereItStarted
     */
    public function sort(array $tickets): array
    {
        if (count($tickets) < 2) {
            return $tickets;
        }

        /** @var TicketInterface[] $ticketsGroupedByOrigin */
        $ticketsGroupedByOrigin = [];
        /** @var TicketInterface[] $ticketsGroupedByDestiny */
        $ticketsGroupedByDestiny = [];

        foreach ($tickets as $ticket) {
            $origin = mb_strtolower($ticket->getOrigin());
            $destiny = mb_strtolower($ticket->getDestiny());

            $ticketsGroupedByOrigin[$origin] = $ticket;
            $ticketsGroupedByDestiny[$destiny] = $ticket;

            if (!isset($ticketsGroupedByOrigin[$destiny])) {
                $ticketsGroupedByOrigin[$destiny] = null;
            }
            if (!isset($ticketsGroupedByDestiny[$origin])) {
                $ticketsGroupedByDestiny[$origin] = null;
            }
        }

        if ($this->countNullValues($ticketsGroupedByDestiny) > 1 || $this->countNullValues($ticketsGroupedByOrigin) > 1) {
            throw new MissingTicketsConnection('There is a missing connection between your tickets.');
        }

        /** @var string $firstDestiny */
        $firstDestiny = null;
        foreach ($ticketsGroupedByDestiny as $destiny => $unused) {
            if (!$ticketsGroupedByDestiny[$destiny]) {
                if ($firstDestiny) {
                    throw new MissingTicketsConnection('There is a missing connection between your tickets.');
                }

                $firstDestiny = $destiny;
            }
        }


        /** @var string $firstDestiny */
        $firstDestiny = null;
        foreach ($ticketsGroupedByDestiny as $destiny => $unused) {
            if (!$ticketsGroupedByDestiny[$destiny]) {
                $firstDestiny = $destiny;
                break;
            }
        }

        if (!$firstDestiny) {
            throw new YourTripEndsWhereItStarted(
                'If your trip end in the same place where it starts, we can not sort your tickets by ' .
                'connections, because it is not possible to determine the first ticket of the trip.'
            );
        }


        $sortedByConnections = [];

        $nextDestiny = $firstDestiny;
        while ($ticket = $ticketsGroupedByOrigin[$nextDestiny]) {
            $sortedByConnections[] = $ticket;
            $nextDestiny = mb_strtolower($ticket->getDestiny());
        };

        return $sortedByConnections;
    }

    private function countNullValues(array $array): int
    {
        $count = 0;
        foreach ($array as $item) {
            if (!$item) {
                $count++;
            }
        }
        return $count;
    }
}