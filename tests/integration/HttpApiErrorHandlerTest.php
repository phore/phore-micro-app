<?php

namespace integration;

use Phore\MicroApp\App;
use Phore\MicroApp\Handler\HttpApiErrorHandler;
use PHPUnit\Framework\TestCase;

class HttpApiErrorHandlerTest extends TestCase
{

    public function testHttpApiErrorResponse() {
        $response = phore_http_request('localhost/testapi/ex/404')->send(false);
        $this->assertEquals(404, $response->getHttpStatus());
        $json = $response->getBodyJson();
        $this->assertEquals("about:blank", $json['type']);
        $this->assertEquals(404, $json['status']);
        $this->assertEquals("Not Found", $json['title']);
    }

}
