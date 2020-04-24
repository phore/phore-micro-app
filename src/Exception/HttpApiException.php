<?php


namespace Phore\MicroApp\Exception;


use Exception;
use Phore\MicroApp\Response\StatusCodes;
use Throwable;

class HttpApiException extends Exception
{
    protected $title;

    public function __construct(
        string $message = "",
        int $code = StatusCodes::HTTP_INTERNAL_SERVER_ERROR,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->title = StatusCodes::getStatusDescription($this->code);
    }

    public function getTitle()
    {
        return $this->title;
    }



}