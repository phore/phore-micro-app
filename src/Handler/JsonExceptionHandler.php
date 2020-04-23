<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 19.06.18
 * Time: 09:44
 */

namespace Phore\MicroApp\Handler;


use Phore\MicroApp\Exception\HttpException;
use Phore\MicroApp\Response\StatusCodes;
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

    /**
     * If true it will provide additional details like trace in the RFC7807 http error JSON
     * @var bool
     */
    private $debugMode = false;
    
    public function addFilter(callable $filter) : self
    {
        $this->filter[] = $filter;
        return $this;
    }
    
    public function setLogger(LoggerInterface $logger) 
    {
        $this->logger = $logger;    
    }

    public function setDebugMode(bool $active)
    {
        $this->debugMode = $active;
    }

    public function __invoke(\Exception $e)
    {
        if ($this->logger !== null) {
            $this->logger->alert("ExceptionHandler: '{$e->getMessage()}' in {$e->getFile()} line {$e->getLine()}\n{$e->getTraceAsString()}");
        }

        $headerAlreadySent = true;
        if ( ! headers_sent($file, $line)) {
            $headerAlreadySent = false;
            if ($e instanceof HttpException) {
                header(StatusCodes::getStatusLine($e->getCode()));
                $problem = $e->getProblemDetails($this->debugMode);
            } else {
                header(StatusCodes::getStatusLine(StatusCodes::HTTP_INTERNAL_SERVER_ERROR));
            }
            header("Content-Type: application/problem+json");
        }

        $problem = [
            "type" => "uri/error/" . str_replace('\\', '/', get_class($e)),
            "title" => StatusCodes::getStatusDescription(StatusCodes::HTTP_INTERNAL_SERVER_ERROR),
            "status" => StatusCodes::HTTP_INTERNAL_SERVER_ERROR,
            "details" =>  "Exception '{$e->getMessage()}' in {$e->getFile()}({$e->getLine()})",
            "instance" => "uri/error/" . time()
        ];
        if($this->debugMode)
            $problem['trace'] = explode("\n", $e->getTraceAsString());
        
        foreach ($this->filter as $curFilter) {
            $error = $curFilter($problem);
            if ($problem === null)
                throw new \InvalidArgumentException("A filter must return something.");
        }
        echo json_encode($problem);
        if ($headerAlreadySent) {
            throw new \InvalidArgumentException("JsonExceptionHandler: Cannot set header(): Output started in $file Line $line. Original Exception Msg: {$e->getMessage()}", 1, $e);
        }
        exit;
    }

}
