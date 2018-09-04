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
     * @var UserProvider
     */
    private $userProvider = null;

    /**
     * @var AuthMech
     */
    private $authMech = null;

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

    public function setRoleMap(array $roleMap) : self
    {
        $this->roleMap = $roleMap;
        return $this;
    }


    public function setUserProvider(UserProvider $userProvider) : self
    {
        $this->userProvider = $userProvider;
        return $this;
    }

    public function setAuthMech (AuthMech $authMech) : self
    {
        $this->authMech = $authMech;
        return $this;
    }

    protected function loadUser ()
    {

        if ($this->authMech instanceof SessionBasedAuthMech) {
            $userId = $this->authMech->getSessionUserId();
            if ($userId !== null) {
                $this->authUser = $this->userProvider->getUserById($userId, $this->roleMap);
                return;
            }
        } else {
            if ($this->authMech->hasAuthData()) {
                $this->authUser
                    = $this->userProvider->validateUser($this->authMech->getAuthToken(),
                    $this->authMech->getAuthPasswd(), $this->roleMap);
                return;
            }
        }
        $this->authMech->requestAuth("");
    }


    public function doLogout()
    {
        if ( ! $this->authMech instanceof SessionBasedAuthMech)
            throw new \InvalidArgumentException("Cannot logout from non session backed authMech");

        $this->authMech->unsetSessionUserId();
    }


    /**
     * @param string $userId
     * @param string $passwd
     *
     * @return bool
     * @throws InvalidUserException
     */
    public function doAuth (string $userId, string $passwd)
    {

        if(($user = $this->userProvider->validateUser($userId, $passwd, $this->roleMap)) !== null) {
            $this->authUser = $user;
            if ($this->authMech instanceof SessionBasedAuthMech)
                $this->authMech->setSessionUserId($userId);
            return true;
        }

        throw new InvalidUserException("Cannot login user '$userId'");
    }


    public function requestAuth(string $message)
    {
        if ($this->authMech === null)
            throw new \InvalidArgumentException("No auth-mech registered / enabled.");
        $this->authMech->requestAuth($message);
    }


    public function getUser()
    {
        if ($this->authUser === null)
            $this->loadUser();
        return $this->authUser;
    }
}
