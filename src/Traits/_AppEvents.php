<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 24.07.18
 * Time: 15:45
 */

namespace Phore\MicroApp\Traits;


use Phore\HttpClient\PhoreHttpResponse;
use Phore\MicroApp\Response\Response;

trait _AppEvents
{



    private $events = [];

    public function onEvent(string $eventName, callable $callback) : self
    {
        if ( ! isset ($this->events[$eventName]))
            $this->events[$eventName] = [];
        $this->events[$eventName][] = $callback;
        return $this;
    }

    /**
     *
     * Return a response if the request
     *
     * @param string $eventName
     * @param array $diParams
     * @return bool
     */
    public function triggerEvent(string $eventName, array $diParams=[]) : ?Response
    {
        if ( ! isset($this->events[$eventName])) {
            return null;
        }
        foreach ($this->events[$eventName] as $curCb) {
            $ret = $curCb(...$this->buildParametersFor($curCb, $diParams));
            if ($ret instanceof Response) {
                return $ret;
            }
        }
        return null;
    }

}
