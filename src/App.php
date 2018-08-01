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
        $this->define("authUser", new DiService(function () {
            $user = $this->authManager->getUser();
            if ($user === null)
                throw new InvalidUserException("Application requests 'authUser', but no user is signed in. (acl rule missing?)");
            return $user;
        }));
        $this->define("mime", new DiService(function() { return new Mime(); }));
    }


    public function addModule(AppModule $module) : self
    {
        $module->register($this);
        return $this;
    }

    private $assets = [];

    public function assets(string $assetRoute = "/assets") : AssetSet
    {
        if ( ! isset ($this->assets[$assetRoute])) {
            $this->acl->addRule(aclRule()->route("{$assetRoute}/*")->methods(["GET", "HEADER"])->ALLOW());
            $assetSet = $this->assets[$assetRoute] = new AssetSet($this);
            $this->router->get("{$assetRoute}/::assetFile", function(RouteParams $routeParams) use ($assetSet) {
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

        $this->acl->validate($request);

        try {
            $ret = $this->router->__dispatchRoute($request);
            if ($ret === true)
                return true;
            if ($this->responseHandler === null)
                throw new \InvalidArgumentException("No response handler defined.");
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