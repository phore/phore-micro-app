<?php


function app() : \Phore\MicroApp\App {
    return \Phore\MicroApp\App::getInstance();
}

function on_route_match (string $route, callable $callback) {

}