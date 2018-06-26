<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 20.06.18
 * Time: 08:59
 */

namespace Phore\MicroApp\Exception;


use Throwable;

class AuthRequiredException extends HttpException
{
    public function __construct(
        string $message = "",
        int $code = 403,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}