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

class Outputs extends Base
{
  /**
   * Return details of a specific output
   *
   * @param integer $output_id  ID of the output file you want details for
   * @param array   $params    Optional overrides
   *
   * @return Output The object representation of the resource
   */
  public function details($output_id, $params = array())
  {
      return new Output($this->proxy->retrieveData("outputs/$output_id.json", array(), $params));
  }

  /**
   * Return progress of a specific output
   *
   * @param integer $output_id  ID of the output file you want progress for
   * @param array   $params    Optional overrides
   *
   * @return Progress The object representation of the resource
   */
  public function progress($output_id, $params = array())
  {
      return new Progress($this->proxy->retrieveData("outputs/$output_id/progress.json", array(), $params));
  }
}
