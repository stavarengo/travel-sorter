<?php
declare(strict_types=1);


namespace TravelSorter\Api\RequestHandler;


use TravelSorter\Api\Response\Response;
use TravelSorter\Api\Response\ResponseBody\Error;
use TravelSorter\Api\Response\ResponseBody\ListOfTickets;
use TravelSorter\Api\Response\ResponseInterface;
use TravelSorter\App\TicketsSorter\Ticket;
use TravelSorter\App\TicketsSorter\TicketInterface;
use TravelSorter\App\TicketsSorter\TicketsSorterInterface;

class PostHandler implements RequestHandlerInterface
{
    /**
     * @var TicketsSorterInterface
     */
    protected $ticketsSorter;

    /**
     * PostHandler constructor.
     * @param TicketsSorterInterface $ticketsSorter
     */
    public function __construct(TicketsSorterInterface $ticketsSorter)
    {
        $this->ticketsSorter = $ticketsSorter;
    }

    /**
     * Handle the received request.
     *
     * @param \stdClass|null $requestBody
     *      The request body, if any.
     *
     * @return ResponseInterface
     */
    public function handleIt(?\stdClass $requestBody): ResponseInterface
    {
        if ($errorResponse = $this->validateRequestBody($requestBody)) {
            return $errorResponse;
        }

        $tickets = $this->convertBodyToArrayOfTickets($requestBody->tickets);

        $tickets = $this->ticketsSorter->sort($tickets);

        return new Response(new ListOfTickets($tickets), 200);
    }

    private function validateRequestBody(?\stdClass $requestBody): ?ResponseInterface
    {
        if (!$requestBody) {
            return new Response(new Error('Missing body content.'), 422);
        }

        if (!isset($requestBody->tickets)) {
            return new Response(new Error('Missing the "tickets" attribute.'), 422);
        }

        $requiredAttributes = [
            'transport',
            'origin',
            'destiny',
        ];

        foreach ($requestBody->tickets as $ticketIndex => $ticketFromBody) {
            foreach ($requiredAttributes as $requiredAttribute) {
                if (!isset($ticketFromBody->$requiredAttribute) || !trim($ticketFromBody->$requiredAttribute)) {
                    return new Response(
                        new Error(
                            sprintf(
                                'There is a problem with the ticket you put in the position "%s". Please provide a value for the "%s" attribute.',
                                $ticketIndex,
                                $requiredAttribute
                            )
                        ),
                        422
                    );
                }
            }
        }

        return null;
    }

    /**
     * @return TicketInterface[]
     */
    private function convertBodyToArrayOfTickets(array $ticketsFromBody): array
    {
        $tickets = [];
        foreach ($ticketsFromBody as $ticketFromBody) {
            $tickets[] = (new Ticket())
                ->setTransport($ticketFromBody->transport)
                ->setOrigin($ticketFromBody->origin)
                ->setDestiny($ticketFromBody->destiny)
                ->setSeat($ticketFromBody->seat ?? null)
                ->setGate($ticketFromBody->gate ?? null)
                ->setExtra($ticketFromBody->extra ?? null);
        }

        return $tickets;
    }
}