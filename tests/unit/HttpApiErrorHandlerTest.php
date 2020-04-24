<?php

namespace unit;

use Phore\MicroApp\Exception\HttpApiException;
use Phore\MicroApp\Handler\HttpApiErrorHandler;
use PHPUnit\Framework\TestCase;

class HttpApiErrorHandlerTest extends TestCase
{
    public function testConstruct() {
        $this->expectExceptionCode(550);
        $this->expectExceptionMessage("blabla");
        $errorHandler = new HttpApiErrorHandler();
        $errorHandler(new HttpApiException());
    }

}
