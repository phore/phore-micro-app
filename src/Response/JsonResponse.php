<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.05.19
 * Time: 15:25
 */

namespace Phore\MicroApp\Response;


class JsonResponse implements Response
{

    protected $jsonData;
    protected $httpResponseCode;
    protected $header = [
        "Content-Type" => "application/json; charset=utf-8",
    ];

    public function __construct(array $jsonData, array $header=null, int $httpResponseCode=200)
    {
        $this->httpResponseCode = $httpResponseCode;
        if ($header !== null) {
            $this->header = array_merge($this->header, $header);
        }
        $this->jsonData = $jsonData;
    }
    
    
    public function send() : bool
    {
        http_response_code($this->httpResponseCode);
        foreach ($this->header as $key => $value)
            header("{$key}: {$value}");
        echo phore_json_encode($this->jsonData);
        return true;
    }
    
}
