<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 26.06.18
 * Time: 15:52
 */

namespace Phore\MicroApp\Auth;


class HttpBasicAuthMech implements AuthMech
{

    public function hasAuthData(): bool
    {
        return isset($_SERVER["PHP_AUTH_USER"]);
    }

    public function getAuthToken() : ?string
    {
        return isset ($_SERVER["PHP_AUTH_USER"]) ? $_SERVER["PHP_AUTH_USER"] : null;
    }

    public function getAuthPasswd() : ?string
    {
        return isset ($_SERVER["PHP_AUTH_PW"]) ? $_SERVER["PHP_AUTH_PW"] : null;
    }

    public function requestAuth(string $message)
    {
        header('WWW-Authenticate: Basic realm="' . $message . '"');
        header('HTTP/1.0 401 Unauthorized');
        echo $message;
        exit;
    }
}
