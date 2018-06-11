<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 15:22
 */
namespace Demo;
use Phore\MicroApp\App;
use Phore\MicroApp\Controller\Controller;
use Phore\MicroApp\Type\QueryParams;
use Phore\MicroApp\Type\Request;
use Phore\MicroApp\Type\Route;
use Phore\MicroApp\Type\RouteParams;

require __DIR__ . "/../vendor/autoload.php";


$app = new App();


$app->route_match("/api/v1/*")
    ->get(function () {

    })
    ->post(function () {

    });




class SafeController extends Controller {

    public function on_get(
        Request $request,
        Route $route,
        RouteParams $routeParams,
        QueryParams $GET
    ) {
        echo "da";
    }
}



$app->route_match("/muh")->delegate(SafeController::class);