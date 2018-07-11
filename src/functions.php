<?php


function app() : \Phore\MicroApp\App {
    return \Phore\MicroApp\App::getInstance();
}


function aclRule(string $alias = null) : \Phore\MicroApp\Auth\AclRule {
    return new \Phore\MicroApp\Auth\AclRule($alias);
}

function href(array $path = []) : string {

}


