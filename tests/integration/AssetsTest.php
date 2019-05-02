<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 02.05.19
 * Time: 18:23
 */

namespace Test\Integration;


use PHPUnit\Framework\TestCase;

class AssetsTest extends TestCase
{

    const TEST_URL = "http://localhost/test";

    public function testAssetsFound()
    {
        $ret = phore_http_request(self::TEST_URL . "/assets/file.txt")->send();

        $this->assertEquals("testfile", $ret->getBody());
        $this->assertEquals("text/plain", $ret->getContentType());
        $this->assertEquals("UTF-8", $ret->getCharset());
    }

    public function testAssetsCantEscapeFolder()
    {
        $ret = phore_http_request(self::TEST_URL . "/assets/~/index.php")->send(false)->getBodyJson();
        print_r ($ret);
        $this->assertEquals("Bogus characters in assetFile.", $ret["error"]["msg"]);
    }
}