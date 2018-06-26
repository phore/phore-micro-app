<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 20.06.18
 * Time: 08:52
 */

namespace Phore\MicroApp\Auth;


class AuthManager
{



    /**
     * @var UserProvider[]
     */
    private $userProvider = [];

    /**
     * @var AuthMech[]
     */
    private $authMechs = [];

    /**
     * @var AuthUser|null
     */
    private $authUser = null;

    private $roleMap = [
        "@owner" => 5,
        "@admin" => 4,
        "@member" => 3,
        "@user" => 2
    ];

    /**
     * @var AuthMech
     */
    private $lastUsedAuthMech = null;


    public function setRoleMap(array $roleMap) : self
    {
        $this->roleMap = $roleMap;
        return $this;
    }




    public function addUserProvider(UserProvider $userProvider) : self
    {
        $this->userProvider[] = $userProvider;
        return $this;
    }

    public function addAuthMech (AuthMech $authMech) : self
    {
        $this->authMechs[] = $authMech;
        return $this;
    }

    protected function requireValidUser ()
    {
        $useAuthMech = null;
        foreach($this->authMechs as $curMech) {
            if ($curMech->hasAuthData()) {
                $useAuthMech = $curMech;
                break;
            }
            $useAuthMech = $curMech;
        }
        $this->lastUsedAuthMech = $useAuthMech;
        if ($useAuthMech === null)
            throw new \InvalidArgumentException("No matching AuthMech registred in AuthManager.");

        if ( ! $useAuthMech->hasAuthData())
            $useAuthMech->requestAuth("Please authenticate");

        foreach ($this->userProvider as $curUserProvider) {
            if(($user = $curUserProvider->validateUser($useAuthMech->getAuthToken(), $useAuthMech->getAuthPasswd(), $this->roleMap)) !== null) {
                $this->authUser = $user;
            }
        }


    }


    public function requestAuth(string $message)
    {
        $this->lastUsedAuthMech->requestAuth($message);
    }


    public function getUser()
    {
        if ($this->authUser === null)
            $this->requireValidUser();
        return $this->authUser;
    }
}