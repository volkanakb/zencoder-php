<?php
/**
 * Zencoder API client interface.
 *
 * @category Services
 * @package  Services_Zencoder
 * @author   Michael Christopher <m@zencoder.com>
 * @version  Release: 2.1.2
 * @license  http://creativecommons.org/licenses/MIT/MIT
 * @link     http://github.com/zencoder/zencoder-php
 */

namespace Zencoder\Services\Zencoder;

abstract class Object
{
    public $outputs;

    public function __construct($params)
    {
        $this->_update_attributes($params);
    }

    protected function _update_attributes($attributes = array())
    {
        foreach($attributes as $attr_name => $attr_value) {
            if(empty($this->$attr_name)) $this->$attr_name = $attr_value;
        }
    }

    protected function _create_outputs($outputs = array())
    {
        foreach($outputs as $output_attrs) {
            if(!empty($output_attrs->label)) {
                $this->outputs[$output_attrs->label] = new Output($output_attrs);
            } else {
                $this->outputs[] = new Output($output_attrs);
            }
        }
    }
}
