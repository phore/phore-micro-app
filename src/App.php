<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 15:26
 */

namespace Phore\MicroApp;


use Phore\Di\Container\DiContainer;
use Phore\Di\Container\Producer\DiService;
use Phore\Di\Container\Producer\DiValue;
use Phore\MicroApp\Auth\Acl;
use Phore\MicroApp\Auth\AuthManager;
use Phore\MicroApp\Auth\AuthUser;
use Phore\MicroApp\Traits\_AppAssets;
use Phore\MicroApp\Traits\_AppBasicAuth;
use Phore\MicroApp\Traits\_AppEnv;
use Phore\MicroApp\Traits\_AppErrorHandler;
use Phore\MicroApp\Traits\_AppResponse;
use Phore\MicroApp\Traits\_AppRoute;
use Phore\MicroApp\Type\Request;

/**
 * Class App
 *
 * @package Phore\MicroApp
 * @property-read Request $request
 * @property-read AuthManager authManager
 * @property-read Acl     $acl
 * @property-read AuthUser $authUser
 *
 */
class App extends DiContainer
{
    use _AppEnv, _AppRoute, _AppBasicAuth, _AppAssets, _AppErrorHandler, _AppResponse;


    public function __construct()
    {
        parent::__construct();
        if (self::$_instance !== null)
            throw new \InvalidArgumentException("Multiple App instanciation");
        self::$_instance = $this;
        $this->add(get_class($this), new DiValue($this));
        $this->add("request", new DiService(function () { return Request::Build(); }));
        $this->add("authManager", new DiValue($authManager = new AuthManager()));
        $this->add("acl", new DiValue(new Acl($authManager, $this)));
        $this->add("authUser", new DiService(function () { return $this->authManager->getUser(); }));
    }





    public function __get ($name)
    {
        return $this->resolve($name);
    }


    private static $_instance = null;
    public static function getInstance() : self
    {
        if (self::$_instance === null)
            new App();
        return self::$_instance;
    }

}