<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 17:32
 */

namespace Phore\MicroApp\Type;

use http\Exception\InvalidArgumentException;
use Phore\MicroApp\Helper\IPSet;

/**
 * Class Request
 *
 * @package Phore\MicroApp\Type
 *
 * @property-read string $requestMethod     POST/GET/PUT/DELETE
 * @property-read string $requestPath       The Path the route is calculated on
 * @property-read string $remoteAddr         The REMOTE_ADDR or X_FORWARED_FOR
 * @property-read QueryParams $GET
 * @property-read QueryParams $POST
 */
class Request extends Immutable
{

    /**
     * ***SECURITY RELEVANT FUNCTION***
     *
     * Think twice before changing things here.
     *
     * Injecting X-Forwarded-For Headers from outside must be prohibited to
     * not circumvent IP Network restrictions!
     *
     *
     * @return string
     */
    protected static function GetRemoteAddr()
    {
        $ipSet = new IPSet(IPSet::PRIVATE_NETS);
        if ( ! $ipSet->match($_SERVER["REMOTE_ADDR"])) {
            return $_SERVER["REMOTE_ADDR"]; // Remote ADDR is Public Address: Use this! (We want Load-Balancers with private IPs only!)
        }
        if (isset ($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $forwardsRev = array_reverse($forwards = explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]));
            for ($i=0; $i<count ($forwardsRev); $i++) {
                if ( ! $ipSet->match(trim ($forwardsRev[$i]))) {
                    return $forwardsRev[$i]; // Return first non-private IP from last to first
                }
            }
            return $forwards[0]; // Return first IP if no public IP found.
        }
        return $_SERVER["REMOTE_ADDR"]; // Return the private Remote-ADDR
    }

    /**
     * Return the entire request's POST/GET Body
     *
     * @return string
     */
    public function getBody() : string
    {
        if ($this->requestMethod !== "POST" && $this->requestMethod !== "PUT")
            throw new \InvalidArgumentException("Body is only availabe on POST/PUT requests.");
        return file_get_contents("php://input");
    }

    /**
     * json-decode the body
     *
     * @return array
     */
    public function getJsonBody($json_options=null) : array
    {
        $data = json_decode($bodyRaw = $this->getBody(), 512, JSON_PRESERVE_ZERO_FRACTION);
        if ($data === null)
            throw new \InvalidArgumentException("Cannot json-decode body: '$bodyRaw'");
        return $data;
    }


    public static function Build()
    {


        $data = [
            "requestMethod" => strtoupper($_SERVER["REQUEST_METHOD"]),
            "requestPath" => parse_url($_SERVER["REQUEST_URI"])["path"],
            "GET" => new QueryParams($_GET),
            "remoteAddr" => self::GetRemoteAddr()
        ];



        if (isset ($_POST))
            $data["POST"] = new QueryParams($_POST);
        return new self($data);
    }

}
