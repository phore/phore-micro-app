<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 20.06.18
 * Time: 09:03
 */

namespace Phore\MicroApp\Auth;


interface AuthMech
{

    public function hasAuthData() : bool;
    public function getAuthToken() : string;
    public function getAuthPasswd() : string;
    public function requestAuth(string $message);
}