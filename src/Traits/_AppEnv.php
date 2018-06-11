<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 15:41
 */

namespace Phore\MicroApp\Traits;


trait _AppEnv
{
    public function get_remote_addr () : string
    {
        if (isset ($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return trim(explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"])[0]);
        }
        return $_SERVER["REMOTE_ADDR"];
    }
}