<?php

namespace Zencoder\Services\Zencoder;

interface HttpProxy
{
    public function createData($key, $body = '', array $opts = []);

    public function retrieveData($key, array $params = [], array $opts = []);

    public function updateData($key, $body = '', array $opts = []);

    public function deleteData($key, array $opts = []);
}
