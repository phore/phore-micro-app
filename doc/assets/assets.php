<?php

namespace App;

use Phore\MicroApp\App;
use Phore\MicroApp\Type\QueryParams;

require __DIR__ . "/../vendor/autoload.php";

$app = new App();

// Allow all GET-Requests to /asset/-path and subpaths
// By default, assets are mounted on route /asset/...
// $app->acl->addRule(aclRule()->route("/asset/*")->methods(["GET"])->ALLOW());

// 1. Try to find the asset in path /assets/
$app->addAssetSearchPath(__DIR__ . "/assets/");

// 2. If the file was not found in 1) - try to find it in /alt_assets/
$app->addAssetSearchPath(__DIR__ . "/alt_assets/");

// Virtual Assets: Requests to /asset/all.js will be generated from
// the two files below.
$app->addVirtualAsset("all.js", [
   __DIR__ . "/vendor/some/library/assets/library.js",
   __DIR__ . "/vendor/other/library/assets/otherLibrary.js"
]);

$app->serve();                                  // Run the app
