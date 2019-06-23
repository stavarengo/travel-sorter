<?php
declare(strict_types=1);


namespace TravelSorter\Api\RequestHandler;


use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TravelSorter\Api\ResponseBody\Error;
use TravelSorter\Api\ResponseBody\ListOfTickets;
use TravelSorter\App\RequestHandler\RequestHandlerInterface;
use TravelSorter\App\TicketsSorter\Ticket;
use TravelSorter\App\TicketsSorter\TicketInterface;
use TravelSorter\App\TicketsSorter\TicketsSorterInterface;
use function GuzzleHttp\Psr7\stream_for;

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

    public function handleIt(RequestInterface $request): ResponseInterface
    {
        $bodyContent = $request->getBody()->getContents();
        $requestBody = $bodyContent ? (object)json_decode($bodyContent) : null;
        if ($errorResponse = $this->validateRequestBody($requestBody)) {
            return $errorResponse;
        }

        $tickets = $this->convertBodyToArrayOfTickets($requestBody->tickets);

        $tickets = $this->ticketsSorter->sort($tickets);

        return new Response(
            200,
            [
                'Content-Type' => 'application/json'
            ],
            stream_for(new ListOfTickets($tickets))
        );
    }

    private function validateRequestBody(?\stdClass $requestBody): ?ResponseInterface
    {
        if (!$requestBody) {
            return new Response(422, ['Content-Type' => 'application/json'],
                stream_for(new Error('Missing body content.')));
        }

        if (!isset($requestBody->tickets)) {
            return new Response(422, ['Content-Type' => 'application/json'],
                stream_for(new Error('Missing the "tickets" attribute.')));
        }

        $requiredAttributes = [
            'transport',
            'origin',
            'destiny',
        ];

        foreach ($requestBody->tickets as $ticketIndex => $ticketFromBody) {
            foreach ($requiredAttributes as $requiredAttribute) {
                if (!isset($ticketFromBody->$requiredAttribute) || !trim($ticketFromBody->$requiredAttribute)) {
                    return new Response(422, ['Content-Type' => 'application/json'], stream_for(
                            new Error(
                                sprintf(
                                    'There is a problem with the ticket you put in the position "%s". Please provide a value for the "%s" attribute.',
                                    $ticketIndex,
                                    $requiredAttribute
                                )
                            )
                        )
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