<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 26.06.18
 * Time: 14:19
 */

namespace Phore\MicroApp\Auth;


use Phore\MicroApp\App;
use Phore\MicroApp\Helper\IPSet;
use Phore\MicroApp\Router\Router;
use Phore\MicroApp\Type\Request;

class Acl
{

    private $authManager;

    private $app;

    /**
     * @var AclRule[]
     */
    private $rules = [];

    public function __construct(AuthManager $authManager, App $app)
    {
        $this->authManager = $authManager;
        $this->app = $app;
    }

    public function addRule(AclRule $rule) : self
    {
        $this->rules[] = $rule;
        return $this;
    }


    private $isValidated = false;


    /**
     * ***SECURITY RELEVANT FUNCTION***
     *
     * Think twice before changing a thing!
     *
     * Checks the rule agaist the request. Return the Action if rule applies
     * otherwise return null.
     *
     * @param AclRule $rule
     * @param Request $request
     *
     * @return string|null Action if rule applies - null otherwise
     */
    protected function ruleApplies(AclRule $rule, Request $request)
    {
        if (($route = $rule->__get_value("route")) !== null) {
            if ( ! Router::IsMatching($route, $request, $dummy))
                return null;
        }
        if (($methods = $rule->__get_value("methods")) !== null) {
            if ( ! in_array($request->requestMethod, $methods))
                return null;
        }
        if (($networks = $rule->__get_value("networks")) !== null) {
            $networksIpSet = new IPSet(explode(" ", $networks));
            if ( ! $networksIpSet->match($request->requestIp)) {
                return null;
            }
        }
        if (($role = $rule->__get_value("role")) !== null) {
            if ($this->authManager->getUser() === null)
                return null;
            if ( ! $this->authManager->getUser()->hasMinRole($role))
                return null;
        }
        return $rule->__get_value("action");
    }



    public function validate(Request $request)
    {
        $app = $this->app;
        $this->isValidated = true;
        foreach ($this->rules as $curRule) {
            $action = $this->ruleApplies($curRule, $request);
            if ($action === null) {
                continue; // Check next rule
            }

            if ($action === AclRule::ACTION_ALLOW) {
                return true;
            } else if ($action === AclRule::ACTION_DENY) {
                $this->authManager->requestAuth("Access denied by acl.");
                return false;
            } else {
                throw new \InvalidArgumentException("Invalid ACL Action: '{$curRule["action"]}");
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