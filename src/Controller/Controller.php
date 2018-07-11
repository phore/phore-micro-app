<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 18:39
 */

namespace Phore\MicroApp\Controller;

use Phore\MicroApp\App;

trait Controller
{

    /**
     * @var App
     */
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

}