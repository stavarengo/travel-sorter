<?php
declare(strict_types=1);


namespace TravelSorter\Api\Response\ResponseBody;


use TravelSorter\Api\Response\ResponseBodyInterface;
use TravelSorter\App\TicketsSorter\TicketInterface;

class ListOfTickets implements ResponseBodyInterface
{
    /**
     * @var TicketInterface[]
     */
    protected $tickets;

    /**
     * ListOfTickets constructor.
     * @param TicketInterface[] $tickets
     */
    public function __construct(array $tickets)
    {
        $this->tickets = $tickets;
    }

    /**
     * @return TicketInterface[]
     */
    public function getTickets(): array
    {
        return $this->tickets;
    }

    /**
     * Convert the response to its JSON representation.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode([
            'tickets' => array_map(function (TicketInterface $ticket) {
                return [
                    'transport' => $ticket->getTransport(),
                    'origin' => $ticket->getOrigin(),
                    'destiny' => $ticket->getDestiny(),
                    'seat' => $ticket->getSeat(),
                    'gate' => $ticket->getGate(),
                    'extra' => $ticket->getExtra(),
                ];
            }, $this->tickets),
        ]);
    }
}