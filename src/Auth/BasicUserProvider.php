<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 20.06.18
 * Time: 09:16
 */

namespace Phore\MicroApp\Auth;


class BasicUserProvider implements UserProvider
{

    private $passwd;
    private $allowPlainTextPasswd;

    public function __construct(array $passwd = [], bool $allowPlainTextPasswd=false)
    {
        $this->passwd = $passwd;
        $this->allowPlainTextPasswd = $allowPlainTextPasswd;
    }






    public function validateUser(string $userName, string $userPasswd, array $roleMap)
    {
        foreach ($this->passwd as $curLine) {
            $data = explode(":", $curLine);
            if (!count($data) == 3)
                continue;
            [$ctoken, $cpasswd, $crole, $meta] = $data;

            $meta = json_decode($meta);

            if ($userName == $ctoken && (password_verify($userPasswd, $cpasswd) || ($this->allowPlainTextPasswd && $cpasswd === $userPasswd))) {
                if ( ! isset ($roleMap[$crole]))
                    throw new \InvalidArgumentException("User role '$crole' is not defined in roleMap.");
                return new AuthUser([
                    "userName" => $userName,
                    "role" => $crole,
                    "roleMap" => $roleMap,
                    "roleId" => $roleMap[$crole],
                    "meta" => $meta
                ]);
            }
        }
        return null;
    }

    /**
     * @param string $userId
     * @param array  $roleMap
     *
     * @return AuthUser
     * @throws InvalidUserException
     */
    public function getUserById(string $userId, array $roleMap) : AuthUser
    {
        foreach ($this->passwd as $curLine) {
            $data = explode(":", $curLine);
            if (!count($data) == 3)
                continue;
            [$ctoken, $cpasswd, $crole, $meta] = $data;

            $meta = json_decode($meta);

            if ($userId == $ctoken) {
                if ( ! isset ($roleMap[$crole]))
                    throw new \InvalidArgumentException("User role '$crole' is not defined in roleMap.");
                return new AuthUser([
                    "userName" => $userId,
                    "role" => $crole,
                    "roleMap" => $roleMap,
                    "roleId" => $roleMap[$crole],
                    "meta" => $meta
                ]);
            }
        }
        throw new InvalidUserException("Invalid userId: '$userId'");
    }
}