<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 19.06.18
 * Time: 10:53
 */

namespace Phore\MicroApp\Exception;


use Throwable;

class HttpException extends \Exception
{
    
    public $responseBody = null;

    public function __construct(
        string $message = "",
        int $code = 500,
        $responseBody = null,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->responseBody = $responseBody;
    }

}
