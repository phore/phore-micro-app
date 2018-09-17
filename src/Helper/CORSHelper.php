<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 17.09.18
 * Time: 11:52
 */

namespace Phore\MicroApp\Helper;


class CORSHelper
{


    public static function SendHeader($allowOrigin="*", $allowMethods="POST, GET, OPTIONS")
    {
        header("Access-Control-Allow-Origin: $allowOrigin");
        header("Access-Control-Allow-Methods: $allowMethods");
    }


}
