<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 20.06.18
 * Time: 08:42
 */

namespace Phore\MicroApp\Auth;


use Phore\MicroApp\Type\Immutable;

/**
 * Class AuthUser
 *
 * @package Phore\MicroApp\Auth
 * @property-read $userName string
 * @property-read $roleMap  string[]
 * @property-read $roleId   int
 * @property-read $role     string
 * @property-read $meta     array
 */
class AuthUser extends Immutable
{


    public function hasMinRole(string $role)
    {
        if ( ! isset ($this->roleMap[$role]))
            throw new \InvalidArgumentException("Invalid role '$role'");
        return $this->roleId >= $this->roleMap[$role];
    }


}