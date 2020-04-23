<?php

namespace unit;

use Phore\MicroApp\Exception\HttpException;
use Phore\MicroApp\Response\StatusCodes;
use PHPUnit\Framework\TestCase;

class HttpExceptionTest extends TestCase
{
    public function testThrowException () {
        $this->expectExceptionMessage("test");
        throw new HttpException("test", 400, "body");
    }

    public function testGetProblemDetails() {
        $e = new HttpException("Bad Request Details", StatusCodes::HTTP_BAD_REQUEST);
        $problemDetails = $e->getProblemDetails(true);
        print_r($problemDetails);
        $this->assertEquals(StatusCodes::HTTP_BAD_REQUEST, $problemDetails['status']);
    }

}
