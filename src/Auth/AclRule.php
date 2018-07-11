<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.07.18
 * Time: 13:38
 */

namespace Phore\MicroApp\Auth;


class AclRule
{

    const ACTION_ALLOW = "ALLOW";
    const ACTION_DENY = "DENY";
    const ACTION_REJECT = "REJECT";


    private $action = self::ACTION_REJECT;

    private $rejectHttpCode = 403;
    private $rejectMessage = "No action specified. Acl rule applies.";

    private $alias = null;

    private $role = null;

    private $route = null;

    private $methods = null;

    private $networks = null;

    public function __construct(string $alias=null)
    {
        $this->alias = $alias;
    }

    public function role(string $role) : self
    {
        $this->role = $role;
        return $this;
    }

    public function route(string $route) : self
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @param string[] $methods
     *
     * @return AclRule
     */
    public function methods(array $methods) : self
    {
        $this->methods = $methods;
        return $this;
    }


    /**
     * Specify Request IP Networks in CIDR Format as array
     *
     * - "127.0.0.1/32"
     *
     * or multiple nets
     *
     * - "127.0.0.1/32 192.168.0.123/24"
     *
     *
     * @param array $networks
     *
     * @return AclRule
     */
    public function networks(string $networks) : self
    {
        $this->networks = $networks;
        return $this;
    }


    public function __get_value($name)
    {
        return $this->$name;
    }

    /**
     * Allow the Request
     *
     * @param string|null $alias
     *
     * @return AclRule
     */
    public function ALLOW() : self
    {
        $this->action = self::ACTION_ALLOW;
        return $this;
    }

    /**
     * Deny Request and send to Login Page
     *
     * @param string|null $alias
     *
     * @return AclRule
     */
    public function DENY() : self
    {
        $this->action = self::ACTION_DENY;
        return $this;
    }

    /**
     * Reject the Request
     *
     * @param string|null $alias
     *
     * @return AclRule
     */
    public function REJECT(int $http_code=403, string $message="Access denied") : self
    {
        $this->rejectHttpCode = $http_code;
        $this->rejectMessage = $message;
        $this->action = self::ACTION_REJECT;
        return $this;
    }
}