<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 18.06.18
 * Time: 15:04
 */

namespace Phore\MicroApp\Traits;


use Phore\MicroApp\Exception\HttpException;
use Phore\MicroApp\Router\Router;
use Phore\MicroApp\Type\Request;
use Phore\MicroApp\Type\Route;
use Phore\MicroApp\Type\RouteParams;

trait _AppAssets
{

    protected $assetPath = [];
    protected $virtualAsset = [];

    protected $mimeTab = [
        "css" => "text/css",
        "js"  => "text/javascript",
        "png" => "image/png",
        "jpg" => "image/jpg",
        "gif" => "image/gif"
    ];

    public function addAssetPath(string $path)
    {
        $this->assetPath[] = $path;
        return $this;
    }



    public function addVirtualAsset($name, $files)
    {
        if ( ! isset ($this->virtualAsset[$name]))
            $this->virtualAsset[$name] = [];
        if ( ! is_array($files))
            $files = [$files];
        foreach ($files as $file)
            $this->virtualAsset[$name][] = $file;
    }


    protected function dispatchAssetRoute(Request $request)
    {
        if ( ! Router::IsMatching("/asset/::assetPath", $request, $params))
            return false;


        $assetPath = $params["assetPath"];
        $ext = pathinfo($assetPath, PATHINFO_EXTENSION);
        if ( ! isset ($this->mimeTab[$ext]))
            throw new \InvalidArgumentException("No mime type defined for file extension '$ext'");

        if (isset ($this->virtualAsset[$assetPath])) {
            header("Content-Type: {$this->mimeTab[$ext]}");
            foreach ($this->virtualAsset[$assetPath] as $curFile) {
                echo file_get_contents($curFile) . "\n";
            }
            exit;
        }

        foreach ($this->assetPath as $curPath) {
            if (file_exists($curPath . "/" . $assetPath)) {
                header("Content-Type: {$this->mimeTab[$ext]}");
                $fp = fopen($curPath . "/" . $assetPath, "r");
                fpassthru($fp);
                fclose($fp);
                exit;
            }
        }
        throw new HttpException("Asset '$assetPath' not found.", 404);
    }

}