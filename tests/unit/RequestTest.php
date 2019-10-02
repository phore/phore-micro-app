<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 02.10.19
 * Time: 11:20
 */

namespace Test;

use Phore\MicroApp\Type\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    public function testGetYMLBody()
    {
        $yml = ;
        $ret = phore_http_request(self::TEST_URL . "/post/str")->withPostData($yml)->send()->get();
        $this->assertEquals($yml, $ret);
    }

    public function testGetYMLException()
    {

    }

    public function testGetStructBodyJson()
    {

    }
    public function testGetStructBodyYml()
    {

    }
    public function testGetStructBodyException()
    {

    }
}
