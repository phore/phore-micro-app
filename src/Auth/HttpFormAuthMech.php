<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 09.07.18
 * Time: 14:59
 */

namespace Phore\MicroApp\Auth;


class HttpFormAuthMech implements AuthMech, SessionBasedAuthMech
{

    public function hasAuthData(): bool
    {
        return false;
    }

    public function getAuthToken(): ?string
    {
        return $_POST["username"];
    }

    public function getAuthPasswd(): ?string
    {
        return $_POST["password"];
    }

    public function requestAuth(string $message)
    {
        header("Location: /login");
        exit;
    }

    public function getSessionUserId()
    {
        if ( ! isset ($_SESSION)) {
            session_start();
        }
        if (isset($_SESSION["authUserId"]))
            return $_SESSION["authUserId"];
        return null;
    }

    public function setSessionUserId(string $userId)
    {
        if ( ! isset ($_SESSION)) {
            session_start();
        }
        $_SESSION["authUserId"] = $userId;
    }

    public function unsetSessionUserId()
    {
        if ( ! isset ($_SESSION)) {
            session_start();
        }
        $_SESSION["authUserId"] = null;
    }
}
