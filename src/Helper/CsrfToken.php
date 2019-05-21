<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 06.05.19
 * Time: 15:01
 */

namespace Phore\MicroApp\Helper;


class CsrfToken
{

    private $newToken = null;

    public function __construct()
    {
        if ( ! isset ($_COOKIE["Csrf-token"])) {
            setcookie("Csrf-token", $this->newToken = phore_random_str(24), 0, "/");
        }
    }

    public function getToken()
    {
        if ($this->newToken !== null)
            return $this->newToken;
        if ( ! isset ($_COOKIE["Csrf-token"]))
            throw new \InvalidArgumentException("Missing 'Csrf-token'-cookie.");
        return $_COOKIE["Csrf-token"];
    }


    public function validate() : bool
    {
        $token = null;
        if (isset($_SERVER["HTTP_X_XSRF_TOKEN"]))
            $token = $_SERVER["HTTP_X_XSRF_TOKEN"];
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]))
            $token = $_SERVER["HTTP_X_REQUESTED_WITH"];
        if (isset($_SERVER["HTTP_X_REQUESTED_BY"]))
            $token = $_SERVER["HTTP_X_REQUESTED_BY"];
        if (isset($_SERVER["HTTP_X_REQUESTED_BY"]))
            $token = $_SERVER["HTTP_X_REQUESTED_BY"];
        if (isset ($_POST) && isset($_POST["csrftoken"]))
            $token = $_POST["csrftoken"];
        if ($token === null)
            throw new \InvalidArgumentException("Can't detect XSRF-Token from header or Post-Data. (csrftoken)");
        if ($token !== $this->getToken())
            throw new \InvalidArgumentException("Validation of XSRF Token failed.");
        return true;
    }


}
