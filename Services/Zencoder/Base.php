<?php

namespace Zencoder\Services\Zencoder;

abstract class Base implements HttpProxy
{
    protected $proxy;

    public function __construct(HttpProxy $proxy)
    {
        $this->proxy = $proxy;
    }

    public function createData($path, $body = '', array $opts = [])
    {
        return $this->proxy->createData($path, $body, $opts);
    }

    public function retrieveData($path, array $params = [], array $opts = [])
    {
        return $this->proxy->retrieveData($path, $params, $opts);
    }

    public function updateData($path, $body = '', array $opts = [])
    {
        return $this->proxy->updateData($path, $body, $opts);
    }

    public function deleteData($path, array $opts = [])
    {
        return $this->proxy->deleteData($path, $opts);
    }
}
