<?php

namespace integration;

use Phore\MicroApp\App;
use Phore\MicroApp\Handler\HttpApiErrorHandler;
use PHPUnit\Framework\TestCase;

class HttpApiErrorHandlerTest extends TestCase
{
    public function testApiErrorHandler() {
        $response = phore_http_request('localhost')->send()->getBodyJson();
    }

}
