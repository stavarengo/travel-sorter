<?php
declare(strict_types=1);


namespace TravelSorter\App\TicketsSorter;


class Ticket implements TicketInterface
{
    /**
     * @var string
     */
    protected $transport;

    /**
     * @var string
     */
    protected $origin;

    /**
     * @var string
     */
    protected $destiny;

    /**
     * @var ?string
     */
    protected $seat;

    /**
     * @var ?string
     */
    protected $gate;

    /**
     * @var ?string
     */
    protected $extra;

    /**
     * @return string
     */
    public function getTransport(): string
    {
        return $this->transport;
    }

    /**
     * @param string $transport
     * @return Ticket
     */
    public function setTransport(string $transport): Ticket
    {
        $this->transport = $transport;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrigin(): string
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     * @return Ticket
     */
    public function setOrigin(string $origin): Ticket
    {
        $this->origin = $origin;
        return $this;
    }

    /**
     * @return string
     */
    public function getDestiny(): string
    {
        return $this->destiny;
    }

    /**
     * @param string $destiny
     * @return Ticket
     */
    public function setDestiny(string $destiny): Ticket
    {
        $this->destiny = $destiny;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSeat(): ?string
    {
        return $this->seat;
    }

    /**
     * @param mixed $seat
     * @return Ticket
     */
    public function setSeat(?string $seat): Ticket
    {
        $this->seat = $seat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGate(): ?string
    {
        return $this->gate;
    }

    /**
     * @param mixed $gate
     * @return Ticket
     */
    public function setGate(?string $gate): Ticket
    {
        $this->gate = $gate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtra(): ?string
    {
        return $this->extra;
    }

    /**
     * @param mixed $extra
     * @return Ticket
     */
    public function setExtra(?string $extra): Ticket
    {
        $this->extra = $extra;
        return $this;
    }

}