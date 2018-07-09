<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 20.06.18
 * Time: 08:53
 */

namespace Phore\MicroApp\Auth;


interface UserProvider
{
    public function getUserById(string $userId, array $roleMap) : AuthUser;

    public function validateUser(string $userId, string $passwd, array $roleMap);
}