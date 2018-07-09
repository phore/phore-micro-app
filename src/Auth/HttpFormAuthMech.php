<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 09.07.18
 * Time: 14:59
 */

namespace Phore\MicroApp\Auth;


class HttpFormAuthMech implements AuthMech
{

    public function hasAuthData(): bool
    {
        session_start();
        return true;
    }

    public function getAuthToken(): string
    {
        return $_POST["username"];
    }

    public function getAuthPasswd(): string
    {
        return $_POST["password"];
    }

    public function requestAuth(string $message)
    {
        header("Location: /login");
        exit;
    }
}