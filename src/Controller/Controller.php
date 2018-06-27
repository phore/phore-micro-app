<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 18:39
 */

namespace Phore\MicroApp\Controller;


use Phore\MicroApp\Type\QueryParams;
use Phore\MicroApp\Type\Request;
use Phore\MicroApp\Type\Route;
use Phore\MicroApp\Type\RouteParams;

abstract class Controller
{

    public function on(Request $request, Route $route, RouteParams $routeParams, QueryParams $GET, QueryParams $POST=null) {

    }

    public function on_get(Request $request, Route $route, RouteParams $routeParams, QueryParams $GET) {

    }

    public function on_post(Request $request, Route $route, RouteParams $routeParams, QueryParams $GET, QueryParams $POST) {

    }

    public function on_put(Request $request, Route $route, RouteParams $routeParams, QueryParams $GET, QueryParams $POST=null) {

    }

    public function on_delete(Request $request, Route $route, RouteParams $routeParams, QueryParams $GET, QueryParams $POST) {

    }

}