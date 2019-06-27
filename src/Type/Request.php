<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 17:32
 */

namespace Phore\MicroApp\Type;

use http\Exception\InvalidArgumentException;
use Phore\Core\Exception\InvalidDataException;
use Phore\MicroApp\Exception\AuthRequiredException;
use Phore\MicroApp\Helper\IPSet;

/**
 * Class Request
 *
 * @package Phore\MicroApp\Type
 *
 * @property-read string $requestMethod     POST/GET/PUT/DELETE
 * @property-read string $requestPath       The Path the route is calculated on
 * @property-read string $remoteAddr         The REMOTE_ADDR or X_FORWARED_FOR
 * @property-read string $requestScheme     http/https
 * @property-read string $httpHost          The Hostname called
 * @property-read string $authorizationMethod   basic|bearer|mac
 * @property-read string $authorization         The Authorization code
 * @property-read string $referer          Referer
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
        if ($this->requestMethod !== "POST" && $this->requestMethod !== "PUT" && $this->requestMethod !== "DELETE")
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
        try {
            return phore_json_decode($bodyRaw = $this->getBody());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException("Cannot json-decode body: '$bodyRaw'", 0, $e);
        }
    }

    /**
     * Return the FQDN URL of the request (for backlinks etc.)
     * 
     * @param bool $includeQueryParams
     * @return string
     */
    public function getFqdnUrl(bool $includeQueryParams = false) : string
    {
        return $this->requestScheme . "://" . $this->httpHost . $this->requestPath;
    }

    /**
     * @return null|string
     * @throws AuthRequiredException
     */
    public function getAuthBearerToken() : ?string
    {
        if ($this->authorizationMethod !== "bearer")
            throw new AuthRequiredException("Bearer authorization is required. (Found {$this->authorizationMethod})");
        return $this->authorization;
    }
    
    
    public static function Build()
    {
        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_UPPER);

        $data = [
            "requestMethod" => strtoupper($_SERVER["REQUEST_METHOD"]),
            "requestPath" => parse_url($_SERVER["REQUEST_URI"])["path"],
            "GET" => new QueryParams($_GET),
            "remoteAddr" => self::GetRemoteAddr(),
            "httpHost" => $_SERVER["HTTP_HOST"],
            "authorizationMethod" => null,
            "authorization" => null,
            "referer" => null
        ];
        $data["requestScheme"] = "http";
        if (isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && strtolower($_SERVER["HTTP_X_FORWARDED_PROTO"]) === "https") {
            $data["requestScheme"] = "https";
        }
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $data["requestScheme"] = "https";
        }
        
        if (isset ($headers["REFERER"])) {
            $data["referer"] = $headers["REFERER"];
        }
        
        if (isset($headers["AUTHORIZATION"])) {
            $auth = explode(" ", $headers["AUTHORIZATION"]);
            if (count ($auth) !== 2)
                throw new InvalidDataException("Invalid Authorization header in request.");
            $data["authorizationMethod"] = strtolower($auth[0]);
            if ( ! in_array($data["authorizationMethod"], ["basic", "bearer", "mac"]))
                throw new InvalidDataException("Invalid Authorization header in request. Method unknown.");
            $data["authorization"] = $auth[1];
            if ($data["authorization"] == "")
                throw new InvalidDataException("Invalid Authorization token.");
        }

        if (isset ($_POST))
            $data["POST"] = new QueryParams($_POST);
        return new self($data);
    }

}
