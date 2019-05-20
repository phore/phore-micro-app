<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.05.19
 * Time: 13:35
 */

namespace Phore\MicroApp\Response;


class RedirectResponse implements Response
{

    public $url;
    
    public function __construct($target, array $params = null)
    {
        $this->url = href($target, $params);
    }
    
    public function send() : bool {
        header("Location: {$this->url}");
        echo "<a href=\"{$this->url}\">Click here to if redirect does not function correct</a>";
        return true;
    }


}
