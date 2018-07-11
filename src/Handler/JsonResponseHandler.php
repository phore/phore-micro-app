<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.07.18
 * Time: 16:41
 */

namespace Phore\MicroApp\Handler;


class JsonResponseHandler implements ResponseHandler
{


    public function handle($data): void
    {
        header("Content-Type: application/json");
        echo json_encode($data);
        exit;
    }
}