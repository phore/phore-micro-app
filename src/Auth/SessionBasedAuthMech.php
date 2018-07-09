<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 09.07.18
 * Time: 15:34
 */

namespace Phore\MicroApp\Auth;


interface SessionBasedAuthMech extends AuthMech
{

    public function getSessionUserId();
    public function setSessionUserId(string $userId);
    public function unsetSessionUserId();
}