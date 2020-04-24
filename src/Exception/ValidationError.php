<?php


namespace Phore\MicroApp\Exception;


use Phore\MicroApp\Response\StatusCodes;

class ValidationError extends HttpApiException
{

    /**
     * ValidationError constructor.
     */
    public function __construct(
        string $message = "",
        string $title = "Your request parameters didn't validate.",
        Throwable $previous = null
    )
    {
        parent::__construct($message, StatusCodes::HTTP_BAD_REQUEST, $previous);
        $this->title = $title;
    }
}