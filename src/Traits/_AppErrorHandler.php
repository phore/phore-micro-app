<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 19.06.18
 * Time: 09:29
 */

namespace Phore\MicroApp\Traits;


trait _AppErrorHandler
{


    public function activateExceptionErrorHandlers ()
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            throw new \ErrorException($errstr, $errno, 1, $errfile, $errline);
        });
    }


    protected $onExceptionHandler = [];

    public function setOnExceptionHandler(callable $fn, string $exceptionClass=null)
    {
        if ($exceptionClass === null) {
            $this->onExceptionHandler["default"] = $fn;
            return;
        }
        $this->onExceptionHandler[$exceptionClass] = $fn;
    }

    public function triggerException (\Exception $e)
    {
        if (isset ($this->onExceptionHandler[get_class($e)]))
            return ($this->onExceptionHandler[get_class($e)])($e);
        
        if ( ! isset ($this->onExceptionHandler["default"]))
            throw new \InvalidArgumentException("No exception handler defined. Define a exception handler using App::setOnExceptionHandler()");
        return ($this->onExceptionHandler["default"])($e);
    }

}
