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

    private $users = [];
    private $allowPlainTextPasswd;

    public function __construct(bool $allowPlainTextPasswd=false)
    {
        $this->allowPlainTextPasswd = $allowPlainTextPasswd;
    }



    public function addUser (string $userName, string $passwordHash, string $role, array $metadata) : self
    {
        if (isset ($this->users[$userName]))
            throw new \InvalidArgumentException("Duplicate password entry of user '$userName'");
        $this->users[$userName] = [
            "hash" => $passwordHash,
            "role" => $role,
            "meta" => $metadata
        ];
        return $this;
    }

    public function addUserYamlFile (string $userFile) : self
    {
        $users = yaml_parse_file($userFile);
        if ($users === false)
            throw new \InvalidArgumentException("Cannot parse yaml user-file: '$userFile'.");
        foreach ($users as $index => $userData) {
            if ( ! isset ($userData["user"]) || ! isset($userData["hash"]) || !isset($userData["role"]) || !isset($userData["meta"]))
                throw new \InvalidArgumentException("Required property missing: user | hash | role | meta in index $index of user-file: '$userFile'");
            try {
                $this->addUser($userData["user"], $userData["hash"], $userData["role"], ["meta"]);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException("Cannot add user '{$userData["user"]}': {$e->getMessage()} (from '$userFile')");
            }
        }
        return $this;
    }



    public function validateUser(string $userName, string $userPasswd, array $roleMap)
    {
        if ( ! isset ($this->users[$userName]))
            return null;
        $user = $this->users[$userName];

        if (password_verify($userPasswd, $user["hash"]) || ($this->allowPlainTextPasswd && $user["hash"] === $userPasswd)) {
            if (!isset ($roleMap[$user["role"]]))
                throw new \InvalidArgumentException("User role '{$user["role"]}' is not defined in roleMap.");
            return new AuthUser([
                "userName" => $userName,
                "role" => $user["role"],
                "roleMap" => $roleMap,
                "roleId" => $roleMap[$user["role"]],
                "meta" => $user["meta"]
            ]);
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
    public function getUserById(string $userName, array $roleMap) : AuthUser
    {
        if ( ! isset ($this->users[$userName]))
            throw new InvalidUserException("Invalid userId: '$userName'");
        $user = $this->users[$userName];

        if (!isset ($roleMap[$user["role"]]))
            throw new \InvalidArgumentException("User role '{$user["role"]}' is not defined in roleMap.");
        return new AuthUser([
            "userName" => $userName,
            "role" => $user["role"],
            "roleMap" => $roleMap,
            "roleId" => $roleMap[$user["role"]],
            "meta" => $user["meta"]
        ]);
    }
}