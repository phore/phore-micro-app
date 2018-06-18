<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 15:22
 */
namespace Demo;
use HtmlTheme\Pack\CoreUI\CoreUI;
use HtmlTheme\Pack\CoreUI\CoreUi_Config_PageWithHeader;
use HtmlTheme\Pack\CoreUI\CoreUi_PageWithHeader;
use Phore\MicroApp\App;
use Phore\MicroApp\Controller\Controller;
use Phore\MicroApp\Type\QueryParams;
use Phore\MicroApp\Type\Request;
use Phore\MicroApp\Type\Route;
use Phore\MicroApp\Type\RouteParams;

require __DIR__ . "/../vendor/autoload.php";


$app = new App();
$app->addAssetPath(CoreUI::COREUI_ASSET_PATH);
$app->addVirtualAsset("all.js", CoreUI::COREUI_JS_FILES);
$app->addVirtualAsset("all.css", CoreUI::COREUI_CSS_FILE);
$app->dispatchAssetRoute();



$app->route_match("/api/v1/*")
    ->get(function () {

    })
    ->post(function () {

    });


$app->route_match("/")->get(function () {
    $config = new CoreUi_Config_PageWithHeader();
    $page = new CoreUi_PageWithHeader($config);
    $page->out();
});


class SafeController extends Controller {

    public function on_get(
        Request $request,
        Route $route,
        RouteParams $routeParams,
        QueryParams $GET
    ) {
        echo "da";
        throw new \InvalidArgumentException("asd");
    }
}



$app->route_match("/muh")->delegate(SafeController::class);