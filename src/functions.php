<?php


function app() : \Phore\MicroApp\App {
    return \Phore\MicroApp\App::getInstance();
}


function aclRule(string $alias = null) : \Phore\MicroApp\Auth\AclRule {
    return new \Phore\MicroApp\Auth\AclRule($alias);
}

/**
 * Build a proper encoded route
 * 
 * @param array $path
 * @param array|null $params
 * @return string
 */
function href($path = [], array $params=null) : string {
    if (is_array($path)) {
        for ($i = 0; $i < count($path); $i++) {
            $path[$i] = urlencode(urlencode($path[$i]));
        }
        $path = "/" . implode("/", $path);
    }

    if ($params !== null) {
        if (strpos($path, "?") === false) {
            $path .= "?";
        } else {
            $path .= "&";
        }
        $path .= http_build_query($params);
    }
    return $path;
}



