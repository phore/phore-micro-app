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

    protected $allowedExtensions = [
        "txt", "html",
        "png", "ico", "jpg", "jpeg", "gif", "svg",
        "js", "css",
        "ttf", "woff", "woff2"
    ];


    public function addAssetSearchPath(string $path) : self
    {
        $this->assetSearchPath[] = $path;
        return $this;
    }


    public function addAllowedExtension(string $extension) : self
    {
        $this->allowedExtensions[] = $extension;
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
        return $this;
    }


    public function __dispatch(RouteParams $params)
    {
        $assetPath = $params->get("assetFile");
        $ext = pathinfo($assetPath, PATHINFO_EXTENSION);

        if ( ! in_array(strtolower($ext), $this->allowedExtensions)) {
            throw new \InvalidArgumentException("Asset extension '$ext' is not allowed. Use App::assets()::addAllowedExtension('$ext') to allow.");
        }

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