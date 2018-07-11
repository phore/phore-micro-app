<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 19:13
 */

namespace Phore\MicroApp\Traits;


use Phore\MicroApp\Handler\ResponseHandler;

trait _AppResponse
{

    protected $responseHeader;

    /**
     * @var ResponseHandler
     */
    protected $responseHandler;

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function withResponseHeader($name, $value)
    {
        $this->responseHeader[$name] = $value;
        return $this;
    }

    public function setResponseHandler(ResponseHandler $responseHandler) : self
    {
        $this->responseHandler = $responseHandler;
        return $this;
    }

    public function outJSON ($data, int $httpCode=200)
    {
        //foreach ()
        header("Content-Type: application/json");
        echo json_encode($data);
        exit;
    }

    public function out($data, string $contentType="text/html", int $httpCode=200)
    {
        header("Content-Type: $contentType");
        echo $data;
        exit;
    }

}