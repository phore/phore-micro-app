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
    protected $header = [
        "Content-Type" => "application/json; charset=utf-8",
    ];

    public function __construct(array $jsonData, array $header=null)
    {
        if ($header !== null) {
            $this->header = array_merge($this->header, $header);
        }
        $this->jsonData = $jsonData;
    }
    
    
    public function send() : bool
    {
        foreach ($this->header as $key => $value)
            header("{$key}: {$value}");
        echo phore_json_encode($this->jsonData);
        return true;
    }
    
}
