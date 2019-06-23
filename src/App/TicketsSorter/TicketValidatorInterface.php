<?php
declare(strict_types=1);


namespace TravelSorter\App\TicketsSorter;


interface TicketValidatorInterface
{
    /**
     * Check if a ticket has valid information.
     *
     * @param TicketInterface $value
     *
     * @return string|null
     *  Return null is the ticket is valid.
     *  Return a string describing the error if the ticket is invalid.
     */
    public function validate(TicketInterface $value): ?string;
}