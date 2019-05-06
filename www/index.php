<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 15:22
 */
namespace Demo;

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
use Phore\Theme\CoreUI\CoreUi_Config_PageWithHeader;
use Phore\Theme\CoreUI\CoreUi_PageWithHeader;
use Phore\Theme\CoreUI\CoreUiModule;

require __DIR__ . "/../vendor/autoload.php";

// Configure the App
$app = new App();
$app->activateExceptionErrorHandlers();
$app->setOnExceptionHandler(new JsonExceptionHandler());

// Set Authentication
$app->authManager->setAuthMech(new HttpBasicAuthMech());
$app->authManager->setUserProvider($bup = new BasicUserProvider(true));
$bup->addUserYamlFile(__DIR__ . "/../user-passwd.yml");

//$bup->addUser("admin", "admin", "@admin", ["some"=>"Metadata"]);

$app->acl->addRule(\aclRule()->route("/*")->role("@admin")->ALLOW());

// Add the CoreUI Theme Module (assets)
$app->addModule(new CoreUiModule());

// Define routes, controllers etc.
$app->router->get("/", function () {
    $config = new CoreUi_Config_PageWithHeader();
    $config->mainContent[] = app()->authUser->userName;
    $page = new CoreUi_PageWithHeader($config);
    $page->out();
});

/**
 * Class SafeController
 * @package Demo
 * @internal
 */
class SafeController {
    use Controller;

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

/**
 * Class SomeModule
 * @package Demo
 * @internal
 */
class SomeModule implements AppModule {
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