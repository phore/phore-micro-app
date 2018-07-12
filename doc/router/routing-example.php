<?php

namespace App;

use Phore\MicroApp\App;
use Phore\MicroApp\Type\QueryParams;

require __DIR__ . "/../vendor/autoload.php";

$app = new App();
$app->acl->addRule(aclRule()->ALLOW());         // <- Allow all requests (Access Control Lists)


// Handle HTTP-GET Requests to /
$app->router->get("/", function () {
    echo "<html><b>Hello</b> World</html>";
    return true;                                // Controllers must return boolean true
                                                // to indicate the request was fulfilled
});

// Handle Form-POST Requests to /login
$app->router->post("/login", function (QueryParams $POST) {
    /* Do some login stuff here */
    return true;
});


$app->serve();                                  // Run the app
