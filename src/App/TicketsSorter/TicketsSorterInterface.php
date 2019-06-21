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
     */
    public function sort(array $tickets): array;
}