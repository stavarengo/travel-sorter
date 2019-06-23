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
use TravelSorter\App\TicketsSorter\TicketValidatorInterface;
use function GuzzleHttp\Psr7\stream_for;

class PostHandler implements RequestHandlerInterface
{
    /**
     * @var TicketsSorterInterface
     */
    protected $ticketsSorter;
    /**
     * @var TicketValidatorInterface
     */
    private $ticketValidator;

    /**
     * PostHandler constructor.
     * @param TicketsSorterInterface $ticketsSorter
     * @param TicketValidatorInterface $ticketValidator
     */
    public function __construct(TicketsSorterInterface $ticketsSorter, TicketValidatorInterface $ticketValidator)
    {
        $this->ticketsSorter = $ticketsSorter;
        $this->ticketValidator = $ticketValidator;
    }

    public function handleIt(RequestInterface $request): ResponseInterface
    {
        $bodyContent = $request->getBody()->getContents();
        $requestBody = $bodyContent ? (object)json_decode($bodyContent) : null;

        if (!$requestBody) {
            return new Response(422, ['Content-Type' => 'application/json'],
                stream_for(new Error('Missing body content.')));
        }

        if (!isset($requestBody->tickets)) {
            return new Response(422, ['Content-Type' => 'application/json'],
                stream_for(new Error('Missing the "tickets" attribute.')));
        }

        $tickets = $this->convertBodyToArrayOfTickets($requestBody->tickets);

        foreach ($tickets as $ticketIndex => $ticket) {
            if ($errorMsg = $this->ticketValidator->validate($ticket)) {
                return new Response(
                    422,
                    ['Content-Type' => 'application/json'],
                    stream_for(
                        new Error(
                            sprintf(
                                'There is a problem with the ticket you put in the position "%s". %s',
                                $ticketIndex,
                                $errorMsg
                            )
                        )
                    )
                );
            }
        }


        $tickets = $this->ticketsSorter->sort($tickets);

        return new Response(
            200,
            [
                'Content-Type' => 'application/json'
            ],
            stream_for(new ListOfTickets($tickets))
        );
    }

    /**
     * @return TicketInterface[]
     */
    private function convertBodyToArrayOfTickets(array $ticketsFromBody): array
    {
        $tickets = [];
        foreach ($ticketsFromBody as $ticketFromBody) {
            $tickets[] = (new Ticket())
                ->setTransport($ticketFromBody->transport ?? null)
                ->setOrigin($ticketFromBody->origin ?? null)
                ->setDestiny($ticketFromBody->destiny ?? null)
                ->setSeat($ticketFromBody->seat ?? null)
                ->setGate($ticketFromBody->gate ?? null)
                ->setExtra($ticketFromBody->extra ?? null);
        }

        return $tickets;
    }
}