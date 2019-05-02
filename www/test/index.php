<?php

namespace App;
use Phore\MicroApp\App;
use Phore\MicroApp\Handler\JsonExceptionHandler;
use Phore\MicroApp\Handler\JsonResponseHandler;
use Phore\MicroApp\Type\Body;
use Phore\MicroApp\Type\Params;
use Phore\MicroApp\Type\Request;

require __DIR__ . "/../../vendor/autoload.php";



$app = new App();
$app->setResponseHandler(new JsonResponseHandler());
$app->setOnExceptionHandler(new JsonExceptionHandler());
$app->acl->addRule(aclRule("*")->ALLOW());

$app->assets("/test/assets")->addAssetSearchPath(__DIR__ . "/_assets_dir");
$app->assets("/test/assets")->addVirtualAsset("virtual.txt", __DIR__ . "/_assets_dir/file.txt");

$app->router->onGet("/test/:param1/:param2?", function (string $param1, Params $params, string $param2=null) {
     $ret = [
         "param1" => $param1,
         "param2" => $param2,
         "params" => $params->all()
     ];
     return $ret;
});


$app->router->onPost("/test/post/:what", function (string $what, Body $body) {
    switch($what) {
        case "json":
            return $body->parseJson();
        case "str":
            echo $body->getContents();
            return true;
        default:
            throw new \Exception("Invalid what route");
    }
});


$app->serve();
