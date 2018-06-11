<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 18:30
 */

namespace Phore\MicroApp\Type;


use Phore\MicroApp\App;
use Phore\MicroApp\Controller\Controller;

class _RouteBox
{

    public function __construct(App $app)
    {

    }

    public function delegate(string $controllerClassName)
    {
        return $this;
    }

    public function on (array $methods = ["GET", "POST", "PUT", "DELETE", "HEADER"], callable $fn) : self
    {
        return $this;
    }

    public function get (callable $fn)
    {
        return $this;
    }


    public function post (callable $fn)
    {
        return $this;
    }

    public function else (callable $fn)
    {
        return $this;
    }

}