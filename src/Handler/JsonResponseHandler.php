<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.07.18
 * Time: 16:41
 */

namespace Phore\MicroApp\Handler;


class JsonResponseHandler implements ResponseHandler
{

    /**
     * @var callable[]
     */
    private $filter = [];

    public function addFilter(callable $filter) : self
    {
        $this->filter[] = $filter;
        return $this;
    }
    
    public function handle($data): void
    {
        header("Content-Type: application/json");
        foreach ($this->filter as $curFilter) {
            $data = $curFilter($data);
            if ($data === null)
                throw new InvalidArgumentException("A filter must return something.");
        }
        echo json_encode($data);
        exit;
    }
}