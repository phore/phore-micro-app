<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 24.07.18
 * Time: 15:52
 */

namespace App;

use Phore\MicroApp\App;
use Phore\MicroApp\Type\QueryParams;

require __DIR__ . "/../../vendor/autoload.php";


$app = new App();

$app->onEvent("update", function() {
    echo "A";
});

$app->onEvent("update", function() {
    echo "B";
});


$app->triggerEvent("update");