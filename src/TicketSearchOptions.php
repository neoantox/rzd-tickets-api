<?php

namespace Rudnikov\RzdTicketsApi;

class TicketSearchOptions
{
    private $originCode;

    private $destinationCode;

    private $date;

    /**
     * @return int
     */
    public function getOriginCode()
    {
        return $this->originCode;
    }

    /**
     * @param int $originCode
     */
    public function setOriginCode(int $originCode)
    {
        $this->originCode = $originCode;
    }

    /**
     * @return int
     */
    public function getDestinationCode()
    {
        return $this->destinationCode;
    }

    /**
     * @param int $destinationCode
     */
    public function setDestinationCode(int $destinationCode)
    {
        $this->destinationCode = $destinationCode;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date)
    {
        $this->date = $date;
    }
}