<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 26.06.18
 * Time: 14:19
 */

namespace Phore\MicroApp\Auth;


use Phore\MicroApp\App;
use Phore\MicroApp\Type\Request;

class Acl
{

    private $authManager;

    private $app;

    private $routes = [];

    public function __construct(AuthManager $authManager, App $app)
    {
        $this->authManager = $authManager;
        $this->app = $app;
    }


    public function allow(string $route, $requireMinRole, array $methods=["HEADER", "GET", "POST"]) : self
    {
        $this->routes[] = ["route" => $route, "minRole" => $requireMinRole, "methods" => $methods, "action"=>"ALLOW"];
        return $this;
    }

    public function addRule(array $rule) : self
    {
        $this->routes[] = $rule;
        return $this;
    }


    private $isValidated = false;

    public function validate()
    {
        $app = $this->app;
        $this->isValidated = true;
        foreach ($this->routes as $curRoute) {

            if (isset ($curRoute["route"])) {
                if ( ! $app->is_route_match($curRoute["route"], $dummy))
                    continue;
            }
            if (isset($curRoute["methods"])) {
                if ( ! in_array($app->request->requestMethod, $curRoute["methods"]))
                    continue;
            }
            if (isset($curRoute["minRole"])) {
                if ($this->authManager->getUser() === null)
                    continue;
                if ( ! $this->authManager->getUser()->hasMinRole($curRoute["minRole"]))
                    continue;
            }
            if ( ! isset ($curRoute["action"]))
                throw new \InvalidArgumentException("Invalid action in acl: " . print_r($curRoute, true));
            if ($curRoute["action"] === "ALLOW") {
                return true;
            } else if ($curRoute["action"] === "DENY") {
                $this->authManager->requestAuth("Access denied by acl.");
                return false;
            } else {
                throw new \InvalidArgumentException("Invalid ACL Action: '{$curRoute["action"]}");
            }
        }
        $this->authManager->requestAuth("Access denied by acl.");
        return false;
    }


    public function __destruct()
    {
        if ( ! $this->isValidated)
            throw new \Exception("Securty Exception: Acl::validate() was not called. No access restrictions apply!");
    }

}