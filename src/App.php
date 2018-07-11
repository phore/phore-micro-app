<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 15:26
 */

namespace Phore\MicroApp;


use http\Exception\InvalidArgumentException;
use Phore\Di\Container\DiContainer;
use Phore\Di\Container\Producer\DiService;
use Phore\Di\Container\Producer\DiValue;
use Phore\MicroApp\Auth\Acl;
use Phore\MicroApp\Auth\AuthManager;
use Phore\MicroApp\Auth\AuthUser;
use Phore\MicroApp\Router\Router;
use Phore\MicroApp\Traits\_AppAssets;
use Phore\MicroApp\Traits\_AppEnv;
use Phore\MicroApp\Traits\_AppErrorHandler;
use Phore\MicroApp\Traits\_AppResponse;
use Phore\MicroApp\Type\Request;

/**
 * Class App
 *
 * @package Phore\MicroApp
 * @property-read Request       $request
 * @property-read AuthManager   $authManager
 * @property-read Acl           $acl
 * @property-read AuthUser      $authUser
 * @property-read Router        $router
 *
 */
class App extends DiContainer
{
    use _AppEnv, _AppAssets, _AppErrorHandler, _AppResponse;


    public function __construct()
    {
        parent::__construct();
        if (self::$_instance !== null)
            throw new \InvalidArgumentException("Multiple App instanciation");
        self::$_instance = $this;
        $this->define(get_class($this), new DiValue($this));
        $this->define("app", new DiValue($this));
        $this->define("request", new DiService(function () { return Request::Build(); }));
        $this->define("authManager", new DiValue($authManager = new AuthManager()));
        $this->define("acl", new DiValue(new Acl($authManager, $this)));
        $this->define("router", new DiValue(new Router($this)));
        $this->define("authUser", new DiService(function () { return $this->authManager->getUser(); }));
    }


    public function addModule(AppModule $module) : self
    {
        $module->register($this);
        return $this;
    }


    public function __get ($name)
    {
        return $this->resolve($name);
    }


    public function call($route, array $params = [])
    {

    }


    public function serve(Request $request=null)
    {
        if ($request === null)
            $request = Request::Build();
        $this->define("request", new DiValue($request));

        $this->acl->validate($request);

        try {
            $this->dispatchAssetRoute($request);
            $ret = $this->router->__dispatchRoute($request);
            if ($this->responseHandler === null)
                throw new InvalidArgumentException("No response handler defined.");
            $this->responseHandler->handle($ret);
        } catch (\Exception $e) {
            $this->triggerException($e);
        }
    }


    private static $_instance = null;
    public static function getInstance() : self
    {
        if (self::$_instance === null)
            new App();
        return self::$_instance;
    }

}