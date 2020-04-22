<?php

namespace unit;

use Phore\MicroApp\Response\StatusCodes;
use PHPUnit\Framework\TestCase;

class StatusCodesTest extends TestCase
{
    public function testGetHeader()
    {
        $this->assertEquals("HTTP/1.1 200 OK", StatusCodes::getHeader(200));
    }

    public function testExceptionGetDescriptionUnknown()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("HTTP status code '9999' invalid or unknown.");
        StatusCodes::getHeader(9999);
    }

    public function testIsError()
    {
        self::assertTrue(StatusCodes::isError(400));
    }
}
