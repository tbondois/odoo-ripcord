<?php

namespace Ripoo\Exception;

/**
 * Base Exception for all library exceptions
 * @author Thomas Bondois
 */
class RipooException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        $detailedMsg = "[{$this->getClassLabel()}] $message";
        if ($previous) {
            $detailedMsg.= PHP_EOL." - Previous: ".$previous->getMessage().PHP_EOL;
        }
        parent::__construct($detailedMsg, $code, $previous);
    }

    /**
     * Get instance class name
     * @return string
     */
    public function getClass() : string
    {
        return get_class($this);
    }

    /**
     * Get instance class label (without namespace)
     * @return string
     */
    public function getClassLabel() : string
    {
        $class = $this->getClass();
        $parts = explode('\\', $class);
        if (count($parts)) {
            $class =  end($parts);
        }
        return str_replace('Exception', '', $class);
    }
}
