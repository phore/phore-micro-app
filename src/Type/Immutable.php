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
        if ( ! array_key_exists($name, $this->__immutableData)) {
            if (func_num_args() == 1)
                throw new \InvalidArgumentException("Missing value '$name' in ". get_class($this));
            if ($default instanceof \Exception)
                throw $default;
            return $default;
        }
        return $this->__immutableData[$name];
    }


    public function all() : array
    {
        return $this->__immutableData;
    }


    public function list() : array 
    {
        return array_keys($this->__immutableData);
    }
    
    /**
     * Pluck data
     * 
     * <example>
     * $data = $this->pluck(["meta", "abc"]);
     * </example>
     * 
     * @param $key
     * @param null $default
     * @return null
     * @throws \Exception
     */
    public function pluck ($key, $default=null)
    {
        return phore_pluck($key, $this->__immutableData, $default);
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
