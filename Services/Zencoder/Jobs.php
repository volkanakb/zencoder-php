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

class Jobs extends Base
{
  /**
   * Create a new job
   *
   * @param array  $job     Array of attributes to use when creating the job
   * @param array  $params  Optional overrides
   *
   * @return Job The object representation of the resource
   */
  public function create($job = NULL, $params = array()) {
    if(is_string($job)) {
      $json = trim($job);
    } else if(is_array($job)) {
      $json = json_encode($job);
    } else {
      throw new \Exception(
        'Job parameters required to create job.');
    }
    $request = $this->proxy->createData("jobs", $json, $params);
    if ($request) {
      return new Job($request);
    }
    throw new \Exception('Unable to create job');
  }

  /**
   * List all jobs on your account
   *
   * @param array  $args    Array of filters to use when loading index
   * @param array  $params  Optional overrides
   *
   * @return array An array of Services_Zencoder_Job objects
   */
  public function index($args = array(), $params = array()) {
    $jobs = $this->proxy->retrieveData("jobs.json", $args, $params);
    $results = array();
    foreach($jobs as $job) $results[] = new Job($job);
    return $results;
  }

  /**
   * Return details of a specific job
   *
   * @param integer $job_id   ID of the job you want details for
   * @param array   $params   Optional overrides
   *
   * @return Job The object representation of the resource
   */
  public function details($job_id, $params = array()) {
    return new Job($this->proxy->retrieveData("jobs/$job_id.json", array(), $params));
  }

  /**
   * Return progress of a specific job
   *
   * @param integer $job_id   ID of the job you want progress for
   * @param array   $params   Optional overrides
   *
   * @return Progress The object representation of the resource
   */
  public function progress($job_id, $params = array()) {
    return new Progress($this->proxy->retrieveData("jobs/$job_id/progress.json", array(), $params));
  }

  /**
   * Resubmit a job
   *
   * @param integer  $job_id  ID of the job you want to resubmit
   * @param array    $params  Optional overrides
   *
   * @return bool If the operation was successful
   */
  public function resubmit($job_id, $params = array()) {
    return $this->proxy->updateData("jobs/$job_id/resubmit", "", $params);
  }

  /**
   * Cancel a job
   *
   * @param integer  $job_id  ID of the job you want to cancel
   * @param array    $params  Optional overrides
   *
   * @return bool If the operation was successful
   */
  public function cancel($job_id, $params = array()) {
    return $this->proxy->updateData("jobs/$job_id/cancel", "", $params);
  }
}
