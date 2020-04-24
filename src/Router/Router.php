<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 27.06.18
 * Time: 11:36
 */

namespace Phore\MicroApp\Router;


use http\Exception\InvalidArgumentException;
use Phore\Di\Container\Producer\DiService;
use Phore\Di\Container\Producer\DiValue;
use Phore\MicroApp\App;
use Phore\MicroApp\Controller\Controller;
use Phore\MicroApp\Type\Body;
use Phore\MicroApp\Type\Request;
use Phore\MicroApp\Type\Route;
use Phore\MicroApp\Type\RouteParams;

class Router
{
    /**
     * @var App
     */
    protected $app;

    protected $routes = [];


    public static function IsMatching (string $route, Request $request, &$params) : bool
    {
        $route = preg_replace("|\\*|", '.*', $route);
        $route = preg_replace("|\\:\\:([a-zA-Z0-9\\_]+)|", '(?<$1>.*)', $route);
        $route = preg_replace("|/\\:([a-zA-Z0-9\\_]+)\\?|", '(/(?<$1>[^/]*))?', $route);
        $route = preg_replace("|\\:([a-zA-Z0-9\\_]+)|", '(?<$1>[^/]*)', $route);
        
        $path = $request->requestPath;
        if(preg_match("|^" . $route . "$|", $path, $params)) {
            foreach ($params as $key => $val) {
                if ($val == "") {
                    unset($params[$key]);
                }
            }
            return true;
        }
        return false;
    }


    public function __construct(App $app)
    {
        $this->app = $app;
    }


    public function delegate(string $route, string $className) : self
    {
        $this->routes[] = ["route"=>$route, "delegate"=>$className];
        return $this;
    }


    public function on(string $route="*", array $methods=["GET", "POST", "PUT", "DELETE"], callable $fn) : self
    {
        $this->routes[] = ["route"=>$route, "methods"=>$methods, "call" => $fn];
        return $this;
    }

    /**
     * @param string $route
     * @param callable $fn
     * @deprecated renamed function to onGet()
     * @return Router
     */
    public function get(string $route, callable $fn) : self
    {
        return $this->onGet($route, $fn);
    }

    /**
     * @param string $route
     * @param callable $fn
     * @deprecated renamed function to onPost()
     * @return Router
     */
    public function post(string $route, callable $fn) : self
    {
        return $this->onPost($route, $fn);
    }

    /**
     * @param string $route
     * @param callable $fn
     * @deprecated renamed function to onPut()
     * @return Router
     */
    public function put(string $route, callable $fn) : self
    {
        return $this->onPut($route, $fn);
    }

    /**
     * @param string $route
     * @param callable $fn
     * @deprecated renamed function to onDelete()
     * @return Router
     */
    public function delete(string $route, callable $fn) : self
    {
        return $this->onDelete($route, $fn);
    }

    public function onGet(string $route, callable $fn) : self
    {
        $this->routes[] = ["route"=>$route, "methods"=>["GET"], "call" => $fn];
        return $this;
    }

    public function onPost(string $route, callable $fn) : self
    {
        $this->routes[] = ["route"=>$route, "methods"=>["POST"], "call" => $fn];
        return $this;
    }

    public function onPut(string $route, callable $fn) : self
    {
        $this->routes[] = ["route"=>$route, "methods"=>["PUT"], "call" => $fn];
        return $this;
    }

    public function onDelete(string $route, callable $fn) : self
    {
        $this->routes[] = ["route"=>$route, "methods"=>["DELETE"], "call" => $fn];
        return $this;
    }

    protected function buildCallParams(Request $request, array $routeParams, string $route) : array
    {
        $callParams = [
            "app" => new DiValue($this->app),
            "request" => new DiValue($request),
            "route" =>
                new DiValue(
                    new Route([
                        "routeParams" => $routeParamsObj = new RouteParams($routeParams),
                        "route" => $route,
                        "request" => $request
                    ])
                ),
            "routeParams" => new DiValue($routeParamsObj),
            "GET" => new DiValue($request->GET),
            "body" => new DiValue(new Body($request)),
            "params" => new DiValue($request->GET)
        ];

        foreach ($routeParams as $key => $val) {
            if (isset ($callParams[$key]))
                throw new \InvalidArgumentException("Cannot add route-param '$key': Reserved keyword!");
            $callParams[$key] = new DiValue($val);
        }
        if ($request->has("POST"))
            $callParams["POST"] = $request->POST;
        $callParams["__call_params"] = $callParams;
        return $callParams;
    }


    public function __dispatchRoute(Request $request)
    {
        $ret = null;
        foreach ($this->routes as $curRoute) {
            $routeParams = [];
            $routeMatch = "*";
            if (isset ($curRoute["methods"])) {
                if ( ! in_array($request->requestMethod, $curRoute["methods"])) {
                    continue;
                }
            }
            if (isset($curRoute["route"])) {
                if ( ! $this::IsMatching($curRoute["route"], $request, $routeParams)) {
                    continue;
                }
                $this->app->add("routeParams", new DiService(function() use ($routeParams) {return new RouteParams($routeParams); }));
                $routeMatch = $curRoute["route"];
            }

            $callParams = $this->buildCallParams($request, $routeParams, $routeMatch);
            if (isset ($curRoute["call"])) {
                $fn = $curRoute["call"];
                $ret = $fn(...$this->app->buildParametersFor($fn, $callParams));
                if ($ret !== null)
                    return $ret;
                throw new \LogicException("Route '{$request->requestMethod}:{$request->requestPath}' does not return anything");
            }

            if (isset($curRoute["delegate"])) {
                $className = $curRoute["delegate"];
                if (is_string($className)) {
                    $controller = new $className(
                        ...
                        $this->app->buildParametersForConstructor(
                            $className,
                            $callParams
                        )
                    );
                } else {
                    $controller = $className;
                }

                if (method_exists($controller, "on")) {
                    $ret = $controller->on(
                        ...
                        $this->app->buildParametersFor(
                            [$controller, "on"],
                            $callParams
                        )
                    );
                }
                if ($ret !== null)
                    return $ret;

                switch ($request->requestMethod) {
                    case "GET":
                        if ( ! method_exists($controller, "on_get"))
                            throw new InvalidArgumentException("Controller '$className' is missing on_get() method to handle GET requests.");
                        $ret = $controller->on_get(...
                            $this->app->buildParametersFor([$controller, "on_get"],
                                $callParams));
                        break;
                    case "POST":
                        if ( ! method_exists($controller, "on_post"))
                            throw new InvalidArgumentException("Controller '$className' is missing on_post() method to handle POST requests.");
                        $ret = $controller->on_post(...
                            $this->app->buildParametersFor([$controller, "on_post"],
                                $callParams));
                        break;
                    case "DELETE":
                        if ( ! method_exists($controller, "on_delete"))
                            throw new InvalidArgumentException("Controller '$className' is missing on_delete() method to handle DELETE requests.");
                        $ret = $controller->on_delete(...$this->app->buildParametersFor([
                            $controller,
                            "on_delete"
                        ], $callParams));
                        break;
                    case "PUT":
                        if ( ! method_exists($controller, "on_put"))
                            throw new InvalidArgumentException("Controller '$className' is missing on_put() method to handle PUT requests.");
                        $ret = $controller->on_put(...
                            $this->app->buildParametersFor([$controller, "on_put"],
                                $callParams));
                        break;
                }
                if ($ret !== null)
                    return $ret;
            }
        }
        throw new \InvalidArgumentException("No action fulfilled request / route not defined. ({$request->requestMethod}:{$request->requestPath})");
    }
}
