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

    public $httpStatusCode = 500;

    public function __construct(
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

}