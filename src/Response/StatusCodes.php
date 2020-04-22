<?php

/**
 * HTTP Status Codes and Helper Methods
 * reference: https://httpstatuses.com/
 */

namespace Phore\MicroApp\Response;

class StatusCodes
{
    // [Informational 1xx]
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    // [Successful 2xx]
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NONAUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    // [Redirection 3xx]
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_TEMPORARY_REDIRECT = 307;
    // [Client Error 4xx]
    const errorCodesBeginAt = 400;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_IM_A_TEAPOT = 418;
    const HTTP_MISDIRECTED_REQUEST = 421;
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    const HTTP_LOCKED = 423;
    const HTTP_FAILED_DEPENDENCY = 424;
    const HTTP_TOO_EARLY = 425;
    const HTTP_UPGRADE_REQUIRED = 426;
    const HTTP_PRECONDITION_REQUIRED = 428;
    const HTTP_TOO_MANY_REQUESTS = 429;
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    const HTTP_CONNECTION_CLOSED_WITHOUT_RESPONSE = 444; //NGINX
    const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    const HTTP_CLIENT_CLOSED_REQUEST = 499; //NGINX
    // [Server Error 5xx]
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_VARIANT_ALSO_NEGOTIATES = 506;
    const HTTP_INSUFFICIENT_STORAGE = 507;
    const HTTP_LOOP_DETECTED = 508;
    const HTTP_NOT_EXTENDED = 510;
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;
    const HTTP_NETWORK_CONNECT_TIMEOUT_ERROR = 599;

    private static $description = array(
        // [Informational 1xx]
        100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing',
        // [Successful 2xx]
        200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content',
        205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-Status', 208=> 'Already Reported', 226 => 'IM Used',
        // [Redirection 3xx]
        300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified',
        305 => 'Use Proxy', 307 => 'Temporary Redirect', 308 => 'Permanent Redirect',
        // [Client Error 4xx]
        400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found',
        405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout',
        409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed', 418 => 'I\'m a teapot', 421 => 'Misdirected Request', 422 => 'Unprocessable Entity',
        423 => 'Locked', 424 => 'Failed Dependency', 425 => 'Too Early', 426 => 'Upgrade Required', 428 => 'Precondition Required',
        429 => 'Too Many Requests', 431 => 'Request Header Fields Too Large', 444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons', 499 => 'Client Closed Request',
        // [Server Error 5xx]
        500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable',
        504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 506 => 'Variant Also Negotiates', 507 => 'Insufficient Storage',
        508 => 'Loop Detected', 510 => 'Not Extended', 511 => 'Network Authentication Required', 599 => 'Network Connect Timeout Error'
    );

    public static function getDescription(int $code) : string
    {
        return (string) phore_pluck($code, self::$description, new \InvalidArgumentException("HTTP status code '$code' invalid or unknown."));
    }

    public static function getHeader(int $code) : string
    {
        return "HTTP/1.1 $code " . self::getDescription($code);
    }

    public static function isError(int $code) : bool
    {
        self::getDescription($code);
        return $code >= self::HTTP_BAD_REQUEST;
    }

}