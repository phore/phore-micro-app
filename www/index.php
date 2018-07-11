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
use HtmlTheme\Pack\CoreUI\CoreUiModule;
use Phore\MicroApp\App;
use Phore\MicroApp\AppModule;
use Phore\MicroApp\Auth\AclRule;
use Phore\MicroApp\Auth\BasicUserProvider;
use Phore\MicroApp\Auth\HttpBasicAuthMech;
use Phore\MicroApp\Controller\Controller;
use Phore\MicroApp\Handler\JsonExceptionHandler;
use Phore\MicroApp\Type\QueryParams;
use Phore\MicroApp\Type\Request;
use Phore\MicroApp\Type\Route;
use Phore\MicroApp\Type\RouteParams;

require __DIR__ . "/../vendor/autoload.php";

// Configure the App
$app = new App();
$app->activateExceptionErrorHandlers();
$app->setOnExceptionHandler(new JsonExceptionHandler());

// Set Authentication
$app->authManager->setAuthMech(new HttpBasicAuthMech());
$app->authManager->setUserProvider(new BasicUserProvider(["admin:admin:@admin:{}"], true));
$app->acl->addRule(\aclRule()->route("/*")->ALLOW());

// Add the CoreUI Theme Module (assets)
$app->addModule(new CoreUiModule());

// Define routes, controllers etc.
$app->router->get("/", function () {
    $config = new CoreUi_Config_PageWithHeader();
    $config->mainContent[] = app()->authUser->userName;
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
        //echo "da";
        throw new \InvalidArgumentException("asd");
    }
}

$app->router->delegate("/muh", SafeController::class);


class SomeModule extends Controller implements AppModule {

    public function on_get(
        Request $request,
        Route $route,
        RouteParams $routeParams,
        QueryParams $GET
    ) {
        \app()->outJSON(["brian_says" => "romans go home!"]);
    }

    public function register(App $app)
    {
        $app->router->delegate("/some/route", self::class);
    }
}

$app->addModule(new SomeModule());

// Run the Application
$app->serve();