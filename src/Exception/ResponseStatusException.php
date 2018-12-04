<?php

namespace Ripoo\Exception;


/**
 * @author Thomas Bondois
 */
class ResponseStatusException extends RipooException
{
    /**
     * @var int
     */
    protected $status;

    /**
     * @var string
     */
    protected $statusMessage;


    /**
     * @param string $statusMessage
     * @param bool $status
     * @param \Throwable|null $previous
     */
    public function __construct(string $statusMessage = "", $status = null, \Throwable $previous = null)
    {
        $nCode               = (int)$status;
        $this->status        = $status;
        $this->statusMessage = $statusMessage;

        $message = sprintf("Status (%s) '%s'", $nCode, $this->statusMessage);;
        parent::__construct($message, $nCode, $previous);
    }

    /** Getter for member status
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * @param string $statusMessage
     */
    public function setStatusMessage($statusMessage)
    {
        $this->statusMessage = $statusMessage;
    }




}
