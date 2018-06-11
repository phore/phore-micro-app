<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 17:32
 */

namespace Phore\MicroApp\Type;


class Immutable
{

    private $__immutableData;

    public function __construct(array $data)
    {
        $this->__immutableData = $data;
    }

    public function get(string $name, $default = null)
    {
        if ( ! isset ($this->__immutableData[$name])) {
            if (func_num_args() == 1)
                throw new \InvalidArgumentException("Missing value '$name'");
            if ($default instanceof \Exception)
                throw $default;
            return $default;
        }
        return $this->__immutableData[$name];
    }


    public function has(string $name) : bool
    {
        return isset ($this->__immutableData[$name]);
    }


    public function __get ($name)
    {
        return $this->get($name);
    }


}