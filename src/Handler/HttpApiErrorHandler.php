<?php


namespace Phore\MicroApp\Handler;


use Phore\Core\Exception\NotFoundException;
use Phore\MicroApp\Exception\HttpApiException;
use Phore\MicroApp\Exception\HttpException;
use Phore\MicroApp\Response\StatusCodes;
use Psr\Log\LoggerInterface;

/**
 * RFC7807 compliant error handler for HTTP API errors
 *
 * Renders error messages in the RFC7807 JSON format.
 * Example:
 * <code>
 * {
 *  "type" : "http://apidocs/ex/InvalidInputData",
 *  "title" : "Bad Request",
 *  "status" : 400,
 *  "details" : "Parameter 'age' is not a number in '/submit?age=abc'",
 *  "instance" : "http://errorLogs/Un1q3rr0rID"
 * }
 * </code>
 * https://tools.ietf.org/html/rfc7807
 *
 * @package Phore\MicroApp\Handler
 */
class HttpApiErrorHandler
{

    /**
     * @var null|LoggerInterface
     */
    private $logger;

    /**
     * If true it will provide additional details like trace in the RFC7807 http error JSON
     * @var bool
     */
    private $debugMode;

    /**
     * @var string
     */
    private $errorTypeBaseURI;

    /**
     * @var string
     */
    private $errorInstanceBaseURI;

    /**
     * HttpApiErrorHandler constructor.
     *
     * @param LoggerInterface|null $logger If provided errors will be logged here
     *
     * @param bool $debugMode If true the response will contain an additional field with the error trace
     *
     * @param string $errorTypeBaseURI URI that will be prepended to the exception class name if provided
     *
     * @param string $errorInstanceBaseURI URI that will be prepended to an exception's unique id if provided
     */
    public function __construct(
        ?LoggerInterface $logger = null,
        bool $debugMode = false,
        $errorTypeBaseURI = "",
        $errorInstanceBaseURI = ""
    )    {
        $this->logger = $logger;
        $this->debugMode = $debugMode;
        $this->errorTypeBaseURI = $errorTypeBaseURI;
        $this->errorInstanceBaseURI = $errorInstanceBaseURI;
    }

    /**
     * @param \Exception $ex
     */
    public function __invoke(\Exception $ex)
    {

        $ex = $this->mapException($ex);

        if(is_subclass_of($ex, 'HttpApiException')) {

        }

        $this->logger->alert("ExceptionHandler: '{$ex->getMessage()}' in {$ex->getFile()} line {$ex->getLine()}\n{$ex->getTraceAsString()}");

        $problemDetails = [
            "type" => "uri/error/" . str_replace('\\', '/', get_class($this)),
            "title" => StatusCodes::getStatusDescription($this->code),
            "status" => $this->code,
            "details" => "Exception '$this->message' in {$this->getFile()}({$this->getLine()}): $this->responseBody",
            "instance" => "uri/error/" . time()
        ];

        echo json_encode($problemDetails);
        if ($headerAlreadySent) {
            throw new \InvalidArgumentException("JsonExceptionHandler: Cannot set header(): Output started in $file Line $line. Original Exception Msg: {$e->getMessage()}", 1, $e);
        }
        exit;
    }

    private function log(string $msg)
    {
        if ($this->logger !== null) {
            $this->logger->alert("ExceptionHandler: $msg");
        }
    }

    private function mapException(\Exception $e)
    {
        if ($e instanceof HttpApiException) {
            return $e;
        }
        return new HttpApiException($e->getMessage(), StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e);
    }

}