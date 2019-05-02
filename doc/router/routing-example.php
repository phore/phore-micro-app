<?php

namespace App;

use Phore\MicroApp\App;
use Phore\MicroApp\Type\Body;
use Phore\MicroApp\Type\Params;
use Phore\MicroApp\Type\QueryParams;
use Phore\MicroApp\Type\Request;

require __DIR__ . "/../vendor/autoload.php";

$app = new App();
$app->acl->addRule(aclRule()->ALLOW());         // <- Allow all requests (Access Control Lists)


// HTTP-GET Request: http://localhost/load/someName?id=1234&name=xyz
$app->router->onGet("/load/:name", function (string $name, Params $params) {
    assert ($name === "someName");
    assert ( $params->get("id") === "1234" );
    assert ( $params->get("name") === "xyz");
    return true;
});

// HTTP-POST Request: http://localhost/load/someName?id=1234&name=xyz with post form data
$app->router->onPost("/load/:name", function (string $name, Params $params, Body $body) {
    assert ($name === "someName");
    assert ($body->parseJson()  === "post data");
    assert ($body->getContents() === ["json post  data"]);
    return true;
});


// Handle HTTP-GET Requests to /
$app->router->onGet("/", function () {
    echo "<html><b>Hello</b> World</html>";
    return true;                                // Controllers must return boolean true
                                                // to indicate the request was fulfilled
});

// Handle Form-POST Requests to /login
$app->router->onPost("/login", function (QueryParams $POST) {
    /* Do some login stuff here */
    return true;
});


$app->serve();                                  // Run the app
