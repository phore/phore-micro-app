<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 19.06.18
 * Time: 10:53
 */

namespace Phore\MicroApp\Exception;


use Phore\MicroApp\Response\StatusCodes;
use Throwable;

class HttpException extends \Exception
{
    
    public $responseBody = null;

    public function __construct(
        string $message = "",
        int $code = 500,
        $responseBody = "(HttpException without body)",
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->responseBody = $responseBody;
    }

    public function getProblemDetails(bool $debug = false) : array
    {
        $problem = [
            "type" => "uri/error/" . str_replace('\\', '/', get_class($this)),
            "title" => StatusCodes::getStatusDescription($this->code),
            "status" => $this->code,
            "details" => "Exception '$this->message' in {$this->getFile()}({$this->getLine()}): $this->responseBody",
            "instance" => "uri/error/" . time()
        ];
        if($debug)
            $problem['trace'] = explode("\n", $this->getTraceAsString());

        return $problem;
    }

}
