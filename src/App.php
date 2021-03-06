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
use Phore\MicroApp\Auth\InvalidUserException;
use Phore\MicroApp\Exception\HttpException;
use Phore\MicroApp\Response\JsonResponse;
use Phore\MicroApp\Response\Response;
use Phore\MicroApp\Router\Router;
use Phore\MicroApp\Traits\_AppAssets;
use Phore\MicroApp\Traits\_AppEnv;
use Phore\MicroApp\Traits\_AppErrorHandler;
use Phore\MicroApp\Traits\_AppEvents;
use Phore\MicroApp\Traits\_AppResponse;
use Phore\MicroApp\Type\AssetSet;
use Phore\MicroApp\Type\Mime;
use Phore\MicroApp\Type\Request;
use Phore\MicroApp\Type\RouteParams;

/**
 * Class App
 *
 * @package Phore\MicroApp
 * @property-read Request       $request
 * @property-read AuthManager   $authManager
 * @property-read Acl           $acl
 * @property-read AuthUser      $authUser
 * @property-read Router        $router
 * @property-read Mime          $mime
 *
 */
class App extends DiContainer
{
    use _AppEnv, _AppErrorHandler, _AppResponse, _AppEvents;

    /**
     * First event fired on serve() function
     *
     * $request is available.
     *
     * Executed before acl is checked
     */
    const EVENT_ON_REQUEST = "on_request";


    public function __construct()
    {
        parent::__construct();
        if (self::$_instance !== null)
            throw new \InvalidArgumentException("Multiple App instanciation");
        self::$_instance = $this;
        $this->define(get_class($this), new DiValue($this));
        $this->define("app", new DiValue($this));
        $this->define("request", new DiService(function () { return Request::Build(); }));
        $this->define("routeParams", new DiService(function() { throw new \InvalidArgumentException("di-service: routeParams not available in this stage."); }));
        $this->define("authManager", new DiValue($authManager = new AuthManager()));
        $this->define("acl", new DiValue(new Acl($authManager, $this)));
        $this->define("router", new DiValue(new Router($this)));
        $this->define("authUser", new DiService(function () {
            $user = $this->authManager->getUser();
            if ($user === null)
                throw new InvalidUserException("Application requests 'authUser', but no user is signed in. (acl rule missing?)");
            $this->triggerEvent("AUTH_USER_REQUESTED", ["authUser"=>$user]);
            return $user;
        }));
        $this->define("mime", new DiService(function() { return new Mime(); }));
    }


    public function addModule(AppModule $module) : self
    {
        $module->register($this);
        return $this;
    }

    public function addCtrl(string $className) : self
    {
        $ref = new \ReflectionClass($className);
        if ( ! $ref->hasConstant("ROUTE")) {
            throw new \InvalidArgumentException("Cannot add addCtrl($className): Class $className requires ROUTE constant.");
        }
        $this->router->delegate($ref->getConstant("ROUTE"), $className);
        return $this;
    }

    private $assets = [];

    public function assets(string $assetRoute = "/assets") : AssetSet
    {
        if ( ! isset ($this->assets[$assetRoute])) {
            $this->acl->addRule(aclRule()->route("{$assetRoute}/*")->methods(["GET", "HEADER"])->ALLOW());
            $assetSet = new AssetSet($this);
            $this->router->onGet("{$assetRoute}/::assetFile", function(RouteParams $routeParams) use ($assetSet) {
                $assetFile = $routeParams->get("assetFile");
                if (strpos($assetFile, "..") !== false || strpos($assetFile, "~") !== false) {
                    throw new \InvalidArgumentException("Bogus characters in assetFile.");
                }

                return $assetSet->__dispatch($routeParams);
            });
            $this->assets[$assetRoute] = $assetSet;
        }
        return $this->assets[$assetRoute];
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



        try {
            if (($ret = $this->triggerEvent(self::EVENT_ON_REQUEST)) instanceof Response) {
                return $ret->send();
            }

            $this->acl->validate($request);

            $ret = $this->router->__dispatchRoute($request);
            if ($ret === true)
                return true;
            if ($ret instanceof Response)
                return $ret->send();
            if ($this->responseHandler === null)
                throw new \InvalidArgumentException("No response handler defined.");
            $this->responseHandler->handle($ret);
        } catch (\Error $e) {
            $ret = $this->triggerException(new \ErrorException($e->getMessage(), $e->getCode(), 1, $e->getFile(), $e->getLine(), $e));
            if ($ret instanceof Response) {
                return $ret->send();
            }
        } catch (\Exception $e) {
            $ret = $this->triggerException($e);
            if ($ret instanceof Response) {
                return $ret->send();
            }
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
