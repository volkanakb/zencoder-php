<?php

namespace Zencoder\Services\Zencoder;

class Inputs extends Base
{
    /**
     * Return details of a specific input.
     *
     * @param int   $input_id ID of the input file you want details for
     * @param array $params   Optional overrides
     *
     * @return Input The object representation of the resource
     */
    public function details($input_id, $params = [])
    {
        return new Input($this->proxy->retrieveData("inputs/$input_id.json", [], $params));
    }

    /**
     * Return progress of a specific input.
     *
     * @param int   $input_id ID of the input file you want progress for
     * @param array $params   Optional overrides
     *
     * @return Progress The object representation of the resource
     */
    public function progress($input_id, $params = [])
    {
        return new Progress($this->proxy->retrieveData("inputs/$input_id/progress.json", [], $params));
    }
}
