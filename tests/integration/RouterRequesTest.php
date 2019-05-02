<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 02.05.19
 * Time: 11:14
 */

namespace Test\Integration;


use PHPUnit\Framework\TestCase;

class RouterRequesTest extends TestCase
{
    const TEST_URL = "http://localhost/test";

    public function testBasicGetWithOptionalParameter()
    {
        $ret = phore_http_request(self::TEST_URL . "/p1/p2")->withQueryParams(["a"=>"va"])->send()->getBodyJson();
        $this->assertEquals([
            "param1" => "p1",
            "param2" => "p2",
            "params" => [
                "a" => "va"
            ]

        ], $ret);
    }

    public function testBasicGetWithoutOptionalParameter()
    {
        $ret = phore_http_request(self::TEST_URL . "/p1")->withQueryParams(["a"=>"va"])->send()->getBodyJson();
        $this->assertEquals([
            "param1" => "p1",
            "param2" => null,
            "params" => [
                "a" => "va"
            ]

        ], $ret);
    }


    public function testBasicPostWithJsonData()
    {
        $ret = phore_http_request(self::TEST_URL . "/post/json")->withPostData(["a"=>"va"])->send()->getBodyJson();
        $this->assertEquals(["a" => "va"], $ret);
    }

    public function testBasicPostWithStringData()
    {
        $testStr = "Some&String?with#DataÄß@!2<>";
        $ret = phore_http_request(self::TEST_URL . "/post/str")->withPostData($testStr)->send()->getBody();
        $this->assertEquals($testStr, $ret);
    }
}