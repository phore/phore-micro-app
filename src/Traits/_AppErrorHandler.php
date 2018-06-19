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


    protected $onExceptionHandler = null;

    public function setOnExceptionHandler(callable $fn)
    {
        $this->onExceptionHandler = $fn;
    }

    public function triggerException (\Exception $e)
    {
        ($this->onExceptionHandler)($e);
    }

}