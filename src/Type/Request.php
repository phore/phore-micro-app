<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 17:32
 */

namespace Phore\MicroApp\Type;

/**
 * Class Request
 *
 * @package Phore\MicroApp\Type
 *
 * @property-read string $requestMethod     POST/GET/PUT/DELETE
 * @property-read QueryParams $GET
 * @property-read QueryParams $POST
 */
class Request extends Immutable
{




    public static function Build()
    {
        $data = [
           "requestMethod" => strtoupper($_SERVER["REQUEST_METHOD"]),
           "GET" => new QueryParams($_GET)
        ];
        if (isset ($_POST))
            $data["POST"] = new QueryParams($_POST);
        return new self($data);
    }

}