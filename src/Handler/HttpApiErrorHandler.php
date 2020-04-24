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

        $problemDetails = [
            "type" => "about:blank",
            "title" => $ex->getTitle(),
            "status" => $ex->getCode(),
            "details" => $ex->getMessage(),
        ];
        if(is_subclass_of($ex, 'HttpApiException')) {
            $problemDetails['type'] = $this->errorTypeBaseURI . str_replace('\\', '/', (new \ReflectionClass($ex))->getShortName());
        }
        $errorID = uniqid();
        if(!empty($this->errorInstanceBaseURI)) {
            $problemDetails['instance'] = $this->errorInstanceBaseURI . $errorID;
        }

        if ($this->logger !== null) {
            $loggerInfo = $problemDetails + [
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'traceString' => $ex->getTraceAsString(),
                    'errorId' => $errorID,
                    'exception' => $ex
                ];
            $this->logger->alert("ExceptionHandler: {title}:'{details}' in {file} line {line}, trace: {traceString}", $loggerInfo);
        }



        if($this->debugMode) {
            $problemDetails['traceString'] = explode("\n",$ex->getTraceAsString());
//            $traces = [];
//            foreach($ex->getTrace() as $trace) {
////                $trace['args'][0];
////                $e = new \InvalidArgumentException();
////                $e->
//                $msg = strip_tags(phore_pluck("args.0.xdebug_message", $trace, ""));
//                if(!empty($msg))
//                    $trace['args'][0]['xdebug_message'] = $msg;
//                $traces[] = $trace;
//            }
//            $problemDetails['trace'] = $traces;//$ex->getTrace();
        }

        $headerAlreadySent = true;
        if ( ! headers_sent($file, $line)) {
            $headerAlreadySent = false;
            header(StatusCodes::getStatusLine($ex->getCode()));
            header("Content-Type: application/problem+json");
        }

        echo json_encode($problemDetails);
        if ($headerAlreadySent) {
            throw new \InvalidArgumentException("ExceptionHandler: Cannot set header(): Output started in $file Line $line. Original Exception Msg: {$ex->getMessage()}", 1, $ex);
        }
        exit;
    }

//    private function getProblemDetails(HttpApiException $ex) : array
//    {
//
//    }

    private function mapException(\Exception $e) : HttpApiException
    {
        if ($e instanceof HttpApiException) {
            return $e;
        }
        return new HttpApiException($e->getMessage(), StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e);
    }

}