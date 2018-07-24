<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 24.07.18
 * Time: 15:45
 */

namespace Phore\MicroApp\Traits;


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

    public function triggerEvent(string $eventName, array $diParams=[])
    {
        if ( ! isset($this->events[$eventName])) {
            return 0;
        }
        foreach ($this->events[$eventName] as $curCb) {
            $curCb(...$this->buildParametersFor($curCb, $diParams));
        }
    }

}