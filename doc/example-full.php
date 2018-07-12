<?php

namespace App;

// Use composer's autoloader: see http://getcomposer.org
use Phore\MicroApp\App;
use Phore\MicroApp\Handler\JsonExceptionHandler;

require __DIR__ . "/../vendor/autoload.php";

$app = new App();

// Handle uncaught exceptions/errors internal and return json-formatted error messages
$app->setOnExceptionHandler(new JsonExceptionHandler());

//$app->