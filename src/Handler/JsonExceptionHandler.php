<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 19.06.18
 * Time: 09:44
 */

namespace Phore\MicroApp\Handler;


use Phore\MicroApp\Exception\HttpException;
use Psr\Log\LoggerInterface;

class JsonExceptionHandler
{

    /**
     * @var callable[]
     */
    private $filter = [];

    /**
     * @var null|LoggerInterface
     */
    private $logger = null;
    
    public function addFilter(callable $filter) : self
    {
        $this->filter[] = $filter;
        return $this;
    }
    
    
    public function setLogger(LoggerInterface $logger) 
    {
        $this->logger = $logger;    
    }
    


    public function __invoke(\Exception $e)
    {
        if ($this->logger !== null) {
            $this->logger->alert("ExceptionHandler: '{$e->getMessage()}' in {$e->getFile()} line {$e->getLine()}\n{$e->getTraceAsString()}");
        }
        
        $responseBody = null;
        $headerAlreadySent = true;
        if ( ! headers_sent($file, $line)) {
            $headerAlreadySent = false;
            if ($e instanceof HttpException) {
                header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");
                $responseBody = $e->responseBody;
            } else {
                header("HTTP/1.1 500 Internal Server Error");
            }
            header("Content-Type: application/json");
        }

        
        $error = [
            "error" => [
                "msg" => $e->getMessage(),
                "code" => $e->getCode(),
                "class" => get_class($e),
                "file" => $e->getFile(). "({$e->getLine()})",
                "trace" => explode("\n", $e->getTraceAsString()),
                "errors" => []
            ]
        ];
        if ($responseBody !== null)
            $data = $responseBody;
        
        
        foreach ($this->filter as $curFilter) {
            $error = $curFilter($error);
            if ($error === null)
                throw new \InvalidArgumentException("A filter must return something.");
        }
        echo json_encode($error, JSON_PRESERVE_ZERO_FRACTION|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        if ($headerAlreadySent) {
            throw new \InvalidArgumentException("JsonExceptionHandler: Cannot set header(): Output started in $file Line $line. Original Exception Msg: {$e->getMessage()}", 1, $e);
        }
        exit;
    }

}
