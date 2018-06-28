<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 19.06.18
 * Time: 09:44
 */

namespace Phore\MicroApp\Handler;


use Phore\MicroApp\Exception\HttpException;

class JsonExceptionHandler
{


    public function __invoke(\Exception $e)
    {
        if ($e instanceof HttpException) {
            header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");
        } else {
            header("HTTP/1.1 500 Internal Server Error");
        }
        header("Content-Type: application/json");

        $data = [
            "success" => false,
            "msg" => $e->getMessage(),
            "code" => $e->getCode(),
            "class" => get_class($e),
            "file" => $e->getFile(). "({$e->getLine()})",
            "trace" => explode("\n", $e->getTraceAsString())
        ];
        echo json_encode($data);
        exit;
    }

}