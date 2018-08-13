<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 19.06.18
 * Time: 09:44
 */

namespace Phore\MicroApp\Handler;


use Phore\MicroApp\Exception\HttpException;

class JsonExceptionHandler
{

    /**
     * @var callable[]
     */
    private $filter = [];

    public function addFilter(callable $filter) : self
    {
        $this->filter[] = $filter;
        return $this;
    }


    public function __invoke(\Exception $e)
    {
        if (headers_sent($file, $line)) {
            throw new \InvalidArgumentException(
                "Headers were already sent by $file Line $line", 0, $e
            );
        }
        $responseBody = null;
        if ($e instanceof HttpException) {
            header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");
            $responseBody = $e->responseBody;
        } else {
            header("HTTP/1.1 500 Internal Server Error");
        }
        header("Content-Type: application/json");
        
        
        
        $data = [
            "success" => false,
            "msg" => $e->getMessage(),
            "code" => $e->getCode(),
            "class" => get_class($e),
            "file" => $e->getFile(). "({$e->getLine()})",
            "trace" => explode("\n", $e->getTraceAsString())
        ];
        if ($responseBody !== null)
            $data = $responseBody;
        
        
        foreach ($this->filter as $curFilter) {
            $data = $curFilter($data);
            if ($data === null)
                throw new \InvalidArgumentException("A filter must return something.");
        }
        echo json_encode($data);
        exit;
    }

}
