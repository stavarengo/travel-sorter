<?php
declare(strict_types=1);


namespace TravelSorter\App\TicketsSorter;

/**
 * A trip ticket.
 */
interface TicketInterface
{
    /**
     * The kind of the transportation this ticket is related to.
     *
     * @return string
     */
    public function getTransport(): string;

    /**
     * The origin of the trip.
     * For example: a name of city, airport, etc.
     *
     * @return string
     */
    public function getOrigin(): string;

    /**
     * The destiny of the trip.
     * For example: a name of city, airport, etc.
     *
     * @return string
     */
    public function getDestiny(): string;

    /**
     * The seat where the passenger will sit during the trip.
     *
     * @return string|null
     */
    public function getSeat(): ?string;

    /**
     * The boarding gate.
     *
     * @return string|null
     */
    public function getGate(): ?string;

    /**
     * Any extra information related to the tick.
     * You can use this to say, for example, where the passengers baggage should left before boarding.
     *
     * @return string|null
     */
    public function getExtra(): ?string;

}