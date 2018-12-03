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
        if ($previous) {
            $message.= PHP_EOL." - previous ".get_class($previous)." : ".$previous->getMessage();
        }
        parent::__construct($message, $code, $previous);
    }

}
