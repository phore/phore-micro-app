<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.05.19
 * Time: 15:25
 */

namespace Phore\MicroApp\Response;


class HtmlResponse implements Response
{

    protected $httpResponseCode;
    protected $htmlData;
    protected $header = [
        "Content-Type" => "text/html; charset=utf-8",
        "X-XSS-Protection" => "1; mode=block"
    ];

    public function __construct(string $htmlData, array $header=null,  int $httpResponseCode=200)
    {
        $this->httpResponseCode = $httpResponseCode;
        if ($header !== null) {
            $this->header = array_merge($this->header, $header);
        }
        $this->htmlData = $htmlData;
    }
    
    
    public function send() : bool
    {
        http_response_code($this->httpResponseCode);
        foreach ($this->header as $key => $value)
            header("{$key}: {$value}");
        echo $this->htmlData;
        return true;
    }
    
}
