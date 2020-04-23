<?php

namespace unit;

use Phore\MicroApp\Handler\HttpApiErrorHandler;
use PHPUnit\Framework\TestCase;

class HttpApiErrorHandlerTest extends TestCase
{
    public function testConstruct() {
        $errorHandler = new HttpApiErrorHandler();
    }

}
