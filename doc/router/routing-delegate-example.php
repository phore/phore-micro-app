<?php

namespace App;

use Phore\MicroApp\App;
use Phore\MicroApp\Controller\Controller;
use Phore\MicroApp\Type\QueryParams;

require __DIR__ . "/../vendor/autoload.php";

$app = new App();
$app->acl->addRule(aclRule()->ALLOW());         // <- Allow all requests (Access Control Lists)

// Define a Controller Class
class HelloWorldCtrl {
    use Controller;

    public function on()
    {
        echo "This will be printed in all Requests";
        return null; // Mark request as not fulfilled.
    }

    public function on_get()
    {
        echo "Hello World";
        return true;
    }

    public function on_post()
    {
        echo "Hello World (POST Request)";
        return true;
    }

    public function on_put()
    {
        echo "Hello World (PUT Request)";
        return true;
    }

    public function on_delete()
    {
        echo "Hello World (DELETE Request)";
        return true;
    }
}

$app->router->delegate("/hello/world", HelloWorldCtrl::class);

$app->serve();                                  // Run the app
