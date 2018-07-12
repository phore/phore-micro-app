<?php

namespace App;

use Phore\MicroApp\App;
use Phore\MicroApp\Controller\Controller;
use Phore\MicroApp\Type\QueryParams;

require __DIR__ . "/../vendor/autoload.php";

$app = new App();
$app->acl->addRule(aclRule()->ALLOW());         // <- Allow all requests (Access Control Lists)




class HelloWorldCtrl {
    use Controller;

    public function on_get()
    {
        echo "Hello World";
        return true;
    }
}



// Handle HTTP-GET Requests to /
$app->router->get("/", function () {
    echo "<html><b>Hello</b> World</html>";
    return true;
});

// Handle Form-POST Requests to /login
$app->router->post("/login", function (QueryParams $POST) {
    /* Do some login stuff here */
    return true;
});


$app->serve();                                  // Run the app
