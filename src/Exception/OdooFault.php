<?php

namespace Ripoo\Exception;

/**
 * @author Thomas Bondois
 */
class OdooFault extends \Exception implements RipooExceptionInterface
{
    public function __construct(string $message = "", int $code, \Throwable $previous = null)
    {
        $message = "Fault code $code - $message";
        parent::__construct($message, $code, $previous);
    }

}
