<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 27.06.18
 * Time: 11:36
 */

namespace Phore\MicroApp\Router;


use Phore\MicroApp\App;
use Phore\MicroApp\Controller\Controller;
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
        $route = preg_replace("|\\:([a-zA-Z0-9\\_]+)|", '(?<$1>[^/]*)', $route);

        $path = $request->requestPath;
        if(preg_match("|^" . $route . "$|", $path, $params))
            return true;
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


    public function get(string $route, callable $fn) : self
    {
        $this->routes[] = ["route"=>$route, "methods"=>["GET"], "call" => $fn];
        return $this;
    }

    public function post(string $route, callable $fn) : self
    {
        $this->routes[] = ["route"=>$route, "methods"=>["POST"], "call" => $fn];
        return $this;
    }

    public function put(string $route, callable $fn) : self
    {
        $this->routes[] = ["route"=>$route, "methods"=>["PUT"], "call" => $fn];
        return $this;
    }

    public function delete(string $route, callable $fn) : self
    {
        $this->routes[] = ["route"=>$route, "methods"=>["DELETE"], "call" => $fn];
        return $this;
    }

    protected function buildCallParams(Request $request, array $routeParams, string $route) : array
    {
        $callParams = [
            "request" => $request,
            "route" =>
                new Route([
                    "routeParams" => $routeParams = new RouteParams($routeParams),
                    "route" => $route,
                    "request" => $request
                ]),
            "routeParams" => $routeParams,
            "GET" => $request->GET

        ];
        if ($request->has("POST"))
            $callParams["POST"] = $request->POST;
        return $callParams;
    }


    public function __dispatchRoute(Request $request)
    {
        foreach ($this->routes as $curRoute) {
            $routeParams = [];
            $routeMatch = "*";
            if (isset ($curRoute["method"])) {
                if ( ! in_array($request->requestMethod, $curRoute["method"])) {
                    continue;
                }
            }
            if (isset($curRoute["route"])) {
                if ( ! $this::IsMatching($curRoute["route"], $request, $routeParams)) {
                    continue;
                }
                $routeMatch = $curRoute["route"];
            }

            $callParams = $this->buildCallParams($request, $routeParams, $routeMatch);
            if (isset ($curRoute["call"])) {
                $fn = $curRoute["call"];
                $ret = $fn(...$this->app->buildParametersFor($fn, $callParams));
            } else if (isset($curRoute["delegate"])) {
                $className = $curRoute["delegate"];
                $controller = new $className(...$this->app->buildParametersForConstructor($className, $callParams));
                if ( ! $controller instanceof Controller)
                    throw new \InvalidArgumentException("Class '$className' must be instance of Controller");
                $controller->on(...
                    $this->app->buildParametersFor([$controller, "on"],
                        $callParams));
                switch ($request->requestMethod) {
                    case "GET":
                        $controller->on_get(...
                            $this->app->buildParametersFor([$controller, "on_get"],
                                $callParams));
                        break;
                    case "POST":
                        $controller->on_post(...
                            $this->app->buildParametersFor([$controller, "on_post"],
                                $callParams));
                        break;
                    case "DELETE":
                        $controller->on_delete(...$this->app->buildParametersFor([
                            $controller,
                            "on_delete"
                        ], $callParams));
                        break;
                    case "PUT":
                        $controller->on_put(...
                            $this->app->buildParametersFor([$controller, "on_put"],
                                $callParams));
                        break;
                }
            }
        }
    }
}