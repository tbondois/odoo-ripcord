<?php

namespace Ripoo\Exception;

/**
 * When there is an error or fault declared in Odoo response
 * @author Thomas Bondois
 */
class ResponseFaultException extends ResponseException
{
    /**
     * @var null|int
     */
    protected $faultCode;

    /**
     * @var string
     */
    protected $faultString;

    /**
     * @param string $faultString
     * @param null|int $faultCode
     * @param \Throwable|null $previous
     */
    public function __construct(string $faultString = "", $faultCode = null, \Throwable $previous = null)
    {
        $this->faultCode   = $faultCode;
        $this->faultString = $faultString;
        $exceptionCode     = (int)$faultCode;
        $exceptionMessage  = sprintf("Fault(%s) '%s'", $exceptionCode, $this->faultString);
        parent::__construct($exceptionMessage, $exceptionCode, $previous);
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
