<?php
declare(strict_types=1);


namespace TravelSorter\App\TicketsSorter;


class SortByOriginAlphabetically implements TicketsSorterInterface
{
    public function sort(array $tickets): array
    {
        uasort(
            $tickets,
            function (TicketInterface $a, TicketInterface $b): int {
                return $a->getOrigin() <=> $b->getOrigin();
            }
        );

        return $tickets;
    }
}