<?php

namespace App;
use Phore\MicroApp\App;
use Phore\MicroApp\Exception\HttpApiException;
use Phore\MicroApp\Handler\HttpApiErrorHandler;
use Phore\MicroApp\Handler\JsonExceptionHandler;
use Phore\MicroApp\Handler\JsonResponseHandler;
use Phore\MicroApp\Type\Body;
use Phore\MicroApp\Type\Params;
use Phore\MicroApp\Type\Request;

require __DIR__ . "/../../vendor/autoload.php";



$app = new App();
$app->setResponseHandler(new JsonResponseHandler());
$app->setOnExceptionHandler(new HttpApiErrorHandler());
$app->acl->addRule(aclRule("*")->ALLOW());
//
//$app->assets("/test/assets")->addAssetSearchPath(__DIR__ . "/_assets_dir");
//$app->assets("/test/assets")->addVirtualAsset("virtual.txt", __DIR__ . "/_assets_dir/file.txt");

$app->router->onGet("/testHttpApi/ex/:type", function (string $type) {
    switch($type) {
        case "test":
            throw new HttpApiException("test");
        case "str":
            echo $body->getContents();
            return true;
        default:
            throw new \Exception("Invalid type route");
    }
});

$app->serve();
