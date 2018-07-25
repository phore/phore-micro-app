<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 25.07.18
 * Time: 18:22
 */

namespace Phore\MicroApp\Type;


use Phore\MicroApp\App;
use Phore\MicroApp\Exception\HttpException;

class AssetSet
{

    /**
     * @var App
     */
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    protected $assetSearchPath = [];
    protected $virtualAsset = [];


    public function addAssetSearchPath(string $path) : self
    {
        $this->assetSearchPath[] = $path;
        return $this;
    }



    public function addVirtualAsset($name, $files) : self
    {
        if ( ! isset ($this->virtualAsset[$name]))
            $this->virtualAsset[$name] = [];
        if ( ! is_array($files))
            $files = [$files];
        foreach ($files as $file)
            $this->virtualAsset[$name][] = $file;
    }


    public function __dispatch(RouteParams $params)
    {
        $assetPath = $params["assetPath"];
        $ext = pathinfo($assetPath, PATHINFO_EXTENSION);
        if ( ! isset ($this->m[$ext]))
            throw new \InvalidArgumentException("No mime type defined for file extension '$ext'");

        if (isset ($this->virtualAsset[$assetPath])) {
            header("Content-Type: {$this->app->mime->getContentTypeByExtension($ext)}");
            foreach ($this->virtualAsset[$assetPath] as $curFile) {
                echo file_get_contents($curFile) . "\n";
            }
            exit;
        }

        foreach ($this->assetSearchPath as $curPath) {
            if (file_exists($curPath . "/" . $assetPath)) {
                header("Content-Type: {$this->app->mime->getContentTypeByExtension($ext)}");
                $fp = fopen($curPath . "/" . $assetPath, "r");
                fpassthru($fp);
                fclose($fp);
                exit;
            }
        }
        throw new HttpException("Asset '$assetPath' not found.", 404);
    }
}