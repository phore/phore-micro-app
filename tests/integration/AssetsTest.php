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

    public function testVirtualAssetsFound()
    {
        $ret = phore_http_request(self::TEST_URL . "/assets/virtual.txt")->send();

        $this->assertEquals("testfile\n", $ret->getBody());
        $this->assertEquals("text/plain", $ret->getContentType());
        $this->assertEquals("UTF-8", $ret->getCharset());
    }


    public function testAssetsCantEscapeFolder()
    {
        $ret = phore_http_request(self::TEST_URL . "/assets/~/index.php")->send(false)->getBodyJson();
        $this->assertStringContainsString("Bogus characters in assetFile.", $ret["details"]);
    }

    public function testAssets404RaisedOnNotFound()
    {
        $ret = phore_http_request(self::TEST_URL . "/assets/some/undefined.png")->send(false);
        $this->assertStringContainsString("Asset 'some/undefined.png' not found.", $ret->getBodyJson()["details"]);
        $this->assertEquals(404, $ret->getHttpStatus());
    }

    public function testAssets500RaisedOnUnsecureExtension()
    {
        $ret = phore_http_request(self::TEST_URL . "/assets/some/undefined.php")->send(false);
        $this->assertStringContainsString("Asset extension 'php' is not allowed. (Path: 'some/undefined.php') Use App::assets()::addAllowedExtension('php') to allow.", $ret->getBodyJson()["details"]);
        $this->assertEquals(500, $ret->getHttpStatus());
    }



}