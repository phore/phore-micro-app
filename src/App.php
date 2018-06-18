<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 15:26
 */

namespace Phore\MicroApp;


use Phore\Di\Container\DiContainer;
use Phore\Di\Container\Producer\DiService;
use Phore\Di\Container\Producer\DiValue;
use Phore\MicroApp\Traits\_AppAssets;
use Phore\MicroApp\Traits\_AppBasicAuth;
use Phore\MicroApp\Traits\_AppEnv;
use Phore\MicroApp\Traits\_AppRoute;
use Phore\MicroApp\Type\Request;

/**
 * Class App
 *
 * @package Phore\MicroApp
 * @property-read Request $request
 */
class App extends DiContainer
{
    use _AppEnv, _AppRoute, _AppBasicAuth, _AppAssets;


    public function __construct()
    {
        parent::__construct();
        $this->add(get_class($this), new DiValue($this));
        $this->add("request", new DiService(function () { return Request::Build(); }));
    }


    public function __get ($name)
    {
        return $this->resolve($name);
    }


    private static $_instance = null;
    public static function getInstance() : self
    {
        if (self::$_instance === null)
            self::$_instance = new App();
        return self::$_instance;
    }

}