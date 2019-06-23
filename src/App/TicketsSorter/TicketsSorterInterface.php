<?php
declare(strict_types=1);


namespace TravelSorter\App\TicketsSorter;

interface TicketsSorterInterface
{
    /**
     * Sorts the $tickets.
     *
     * @param TicketInterface[] $tickets
     *  The list of {@link Ticket} to be sorted.
     *
     * @return TicketInterface[]
     *  The sorted list of {@link Ticked}
     *
     * @throws \TravelSorter\App\TicketsSorter\Exception\MissingTicketsConnection
     *  If one of the tickets does not connection to any of the others tickets.
     *
     * @throws \TravelSorter\App\TicketsSorter\Exception\YourTripEndsWhereItStarted
     *  If the journey ends in the same place where it starts.
     */
    public function sort(array $tickets): array;
}