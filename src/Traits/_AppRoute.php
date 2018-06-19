<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 15:39
 */

namespace Phore\MicroApp\Traits;


use Phore\MicroApp\Controller\Controller;
use Phore\MicroApp\Type\_RouteBox;
use Phore\MicroApp\Type\_RouteBoxRouteActive;
use Phore\MicroApp\Type\Request;
use Phore\MicroApp\Type\Route;
use Phore\MicroApp\Type\RouteParams;

trait _AppRoute
{
    protected function is_route_match (string $route, &$params)
    {
        $route = preg_replace("|\\*|", '.*', $route);
        $route = preg_replace("|\\:\\:([a-zA-Z0-9\\_]+)|", '(?<$1>.*)', $route);
        $route = preg_replace("|\\:([a-zA-Z0-9\\_]+)|", '(?<$1>[^/]*)', $route);

        $path = parse_url($_SERVER["REQUEST_URI"])["path"];
        if(preg_match("|" . $route . "|", $path, $params))
            return true;
        return false;
    }


    private $routeMatched = false;

    public function route_match (string $route) : _RouteBox
    {
        if ($this->is_route_match($route, $params)) {
            $request = Request::Build();
            $callParams = [
                "request" => $this->request,
                "route" =>
                      new Route([
                          "routeParams" => $routeParams = new RouteParams($params),
                          "route" => $route,
                          "request" => $request
                      ]),
                "routeParams" => $routeParams,
                "GET" => $request->GET

            ];
            if ($request->has("POST"))
                $callParams["POST"] = $request->POST;

            $this->routeMatched = true;
            return new _RouteBoxRouteActive($this, $callParams);
        }
        return new _RouteBox($this);
    }

    public function __destruct()
    {
        if (!$this->routeMatched)
            throw new \Exception("No route matched request.");
    }
}