<?php

namespace Ripoo\Exception;

/**
 * When Odoo custom API methods declare a bad "status" & "status_message" in response
 * @author Thomas Bondois
 */
class ResponseStatusException extends ResponseException
{
    /**
     * @var bool|int|null
     */
    protected $status;

    /**
     * @var string
     */
    protected $statusMessage;

    /**
     * @param string $statusMessage
     * @param null|bool $status
     * @param \Throwable|null $previous
     */
    public function __construct(string $statusMessage = "", $status = null, \Throwable $previous = null)
    {
        $this->status        = $status;
        $this->statusMessage = $statusMessage;
        $exceptionCode       = (int)$status;
        $exceptionMessage    = sprintf("Status(%s) '%s'", $exceptionCode, $this->statusMessage);;
        parent::__construct($exceptionMessage, $exceptionCode, $previous);
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
