<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 15:37
 */

namespace Phore\MicroApp\Traits;


trait _AppBasicAuth
{
    public function require_basic_auth ($realm="Please login.")
    {
        header('WWW-Authenticate: Basic realm="' . addslashes($realm) . '"');
        header('HTTP/1.0 401 Unauthorized');
        echo '<b>Unauthorized: Please reload page and sign in.</b>';
        exit;
    }

    public function verify_basic_auth (array $allowedUserPasswd, $realm="Please login.", bool $allowPlainTextPasswords = false)
    {
        if ( ! isset ($_SERVER["PHP_AUTH_USER"]) || ! isset ($_SERVER["PHP_AUTH_PW"])) {
            $this->require_basic_auth($realm);
        }
        foreach ($allowedUserPasswd as $key => $value) {
            if (is_int($key)) {
                $exp = explode(":", $value);
                if (count ($exp) != 2)
                    continue;
                $user = $exp[0];
                $pass = $exp[1];
            } else {
                $user = $key;
                $pass = $value;
            }
            if ($user == $_SERVER["PHP_AUTH_USER"] && strlen(trim ($user)) > 2) {
                $pwInfo = password_get_info($pass);
                if ($pwInfo["algo"] == 0 && $allowPlainTextPasswords && $pass == $_SERVER["PHP_AUTH_PW"])
                    return true;
                if (password_verify($_SERVER["PHP_AUTH_PW"], $pass))
                    return true;
            }
        }
        $this->require_basic_auth($realm);
    }
}