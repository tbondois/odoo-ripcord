<?php

namespace Ripoo\Exception;

/**
 * @author Thomas Bondois
 */
class ResponseFaultException extends RipooException
{
    /**
     * @var int
     */
    protected $faultCode;

    /**
     * @var string
     */
    protected $faultString;

    /**
     * @param string $faultString
     * @param ?int $faultCode
     * @param \Throwable|null $previous
     */
    public function __construct(string $faultString = "", $faultCode = null, \Throwable $previous = null)
    {
        $nCode             = (int)$faultCode;
        $this->faultCode   = $faultCode;
        $this->faultString = $faultString;

        $message = sprintf("Fault (%s) '%s'", $nCode, $this->faultString);
        parent::__construct($message, $nCode, $previous);
    }

    /**
     * @return int
     */
    public function getFaultCode()
    {
        return $this->faultCode;
    }

    /**
     * @return string
     */
    public function getFaultString()
    {
        return $this->faultString;
    }

    /**
     * @param string $faultString
     */
    public function setFaultString($faultString)
    {
        $this->faultString = $faultString;
    }

}
