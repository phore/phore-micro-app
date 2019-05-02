<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 02.05.19
 * Time: 10:42
 */

namespace Phore\MicroApp\Type;


class Body
{

    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function parseJson() : array
    {
        return $this->request->getJsonBody();
    }

    public function getContents() : string
    {
        return $this->request->getBody();
    }
}