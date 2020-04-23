<?php


namespace Phore\MicroApp\Exception;


use Exception;
use Phore\MicroApp\Response\StatusCodes;
use Throwable;

class HttpApiException extends Exception
{
    public function __construct(
        string $message = "",
        int $code = StatusCodes::HTTP_INTERNAL_SERVER_ERROR,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }



}