<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 18:49
 */

namespace Phore\MicroApp\Type;


use Phore\MicroApp\App;
use Phore\MicroApp\Controller\Controller;

class _RouteBoxRouteActive extends _RouteBox
{

    private $app;
    private $callParams;

    public function __construct(App $app, array $callParams)
    {
        $this->app = $app;
        $this->callParams = $callParams;
    }

    public function delegate(string $controllerClassName)
    {
        if ( ! class_exists($controllerClassName))
            throw new \InvalidArgumentException("Controller class '$controllerClassName' not exiting" );
        try {
            $controller
                = new $controllerClassName($this->app->buildParametersForConstructor($controllerClassName,
                $this->callParams));
            $controller->on(...
                $this->app->buildParametersFor([$controller, "on"],
                    $this->callParams));
            switch ($this->app->request->requestMethod) {
                case "GET":
                    $controller->on_get(...
                        $this->app->buildParametersFor([$controller, "on_get"],
                            $this->callParams));
                    break;
                case "POST":
                    $controller->on_post(...
                        $this->app->buildParametersFor([$controller, "on_post"],
                            $this->callParams));
                    break;
                case "DELETE":
                    $controller->on_delete(...$this->app->buildParametersFor([
                        $controller,
                        "on_delete"
                    ], $this->callParams));
                    break;
                case "PUT":
                    $controller->on_put(...
                        $this->app->buildParametersFor([$controller, "on_put"],
                            $this->callParams));
                    break;
            }
        } catch (\Exception $e) {
            $this->app->triggerException($e);
        }

        return $this;
    }


    public function on (array $methods = ["GET", "POST", "PUT", "DELETE", "HEADER"], callable $fn) : _RouteBox
    {
        try {
            if (in_array($this->app->request->requestMethod, $methods)) {
                $fn(...$this->app->buildParametersFor($fn, $this->callParams));
            }

            return $this;
        } catch (\Exception $e) {
            $this->app->triggerException($e);
        }
    }

    public function get (callable $fn) : _RouteBox
    {
        try {
            if ($this->app->request->requestMethod == "GET") {
                $fn(...$this->app->buildParametersFor($fn, $this->callParams));
            }

            return $this;
        } catch (\Exception $e) {
            $this->app->triggerException($e);
        }
    }


    public function post (callable $fn) : _RouteBox
    {

        if ($this->app->request->requestMethod == "POST")
            $fn(...$this->app->buildParametersFor($fn, $this->callParams));
        return $this;
    }


    public function __destruct()
    {
        if ( ! headers_sent() && ob_get_length() == 0)
            throw new \Exception("No output after execution of route '" . $this->callParams["route"]->route);
    }


}