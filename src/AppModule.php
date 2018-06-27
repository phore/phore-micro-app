<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 27.06.18
 * Time: 12:35
 */

namespace Phore\MicroApp;


interface AppModule
{

    public function register(App $app);

}