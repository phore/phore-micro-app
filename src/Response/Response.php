<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.05.19
 * Time: 13:34
 */

namespace Phore\MicroApp\Response;


interface Response
{

    public function send() : bool;
    
}
