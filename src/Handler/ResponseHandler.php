<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.07.18
 * Time: 16:41
 */

namespace Phore\MicroApp\Handler;


interface ResponseHandler
{

    public function handle($data) : void;

}