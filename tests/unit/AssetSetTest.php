<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 08.05.19
 * Time: 11:32
 */

namespace Test;


use Phore\MicroApp\App;
use Phore\MicroApp\Testing\MockApp;
use Phore\MicroApp\Type\AssetSet;
use Phore\MicroApp\Type\Request;
use PHPUnit\Framework\TestCase;

class AssetSetTest extends TestCase
{


    public function testAssetAddSearchPathThrowsExceptionOnInvalidPath()
    {
        $this->expectException(\InvalidArgumentException::class);
        $assetSet = new AssetSet(new MockApp());
        $assetSet->addAssetSearchPath("/non/existent/path");
    }

    public function testAssetRejectsRootAsPath()
    {
        $this->expectException(\InvalidArgumentException::class);
        $assetSet = new AssetSet(new MockApp());
        $assetSet->addAssetSearchPath("/");
    }
}