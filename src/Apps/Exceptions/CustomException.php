<?php

namespace CustomFixers\Exceptions;
use Exception;
use Throwable;

class CustomException extends Exception
{
    protected $message = 'Unknown exception';     // Exception message
    protected $code   = 0;                       // User-defined exception code
    public function __construct($message = null, $code = 0, ?\Throwable $previous = null)
    {
        if (!$message) {
            throw new $this('Unknown ' . get_class($this));
        }
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return get_class($this) . " '{$this->message}' in {$this->file} ({$this->line})\n" . "{$this->getTraceAsString()}";
    }
}
