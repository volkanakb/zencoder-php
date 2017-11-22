<?php
/**
 * Zencoder API client interface.
 *
 * @category Services
 * @package  Services_Zencoder
 * @author   Michael Christopher <m@zencoder.com>
 * @version  Release: 2.2.3
 * @license  http://creativecommons.org/licenses/MIT/MIT
 * @link     http://github.com/zencoder/zencoder-php
 * @access   private
 */
namespace Zencoder\Services;

use Zencoder\Services\Zencoder\Accounts;
use Zencoder\Services\Zencoder\Base;
use Zencoder\Services\Zencoder\Http;
use Zencoder\Services\Zencoder\Inputs;
use Zencoder\Services\Zencoder\Jobs;
use Zencoder\Services\Zencoder\Notifications;
use Zencoder\Services\Zencoder\Outputs;

/**
 * Zencoder API client interface.
 *
 * @category Services
 * @package  Services_Zencoder
 * @author   Michael Christopher <m@zencoder.com>
 * @version  Release: 2.2.3
 * @license  http://creativecommons.org/licenses/MIT/MIT
 * @link     http://github.com/zencoder/zencoder-php
 */

class Zencoder extends Base
{
    const USER_AGENT = 'ZencoderPHP v2.2.3';

    /**
    * Contains the HTTP communication class
    * 
    * @var Http
    */
    protected $http;
    /**
    * Contains the default API version
    * 
    * @var string
    */
    protected $version;

    /**
    * Provides access the Zencoder Accounts API
    * 
    * Valid functions: create, details, integration, live
    *
    * @var Accounts
    */
    public $accounts;
    /**
    * Provides access the Zencoder Inputs API
    *
    * Valid functions: details, progress
    *
    * @var Inputs
    */
    public $inputs;
    /**
    * Provides access the Zencoder Jobs API
    *
    * Valid functions: create, index, details, progress, resubmit, cancel
    *
    * @var Jobs
    */
    public $jobs;
    /**
    * Provides access the Zencoder Notifications API
    *
    * Valid functions: parseIncoming
    *
    * @var Notifications
    */
    public $notifications;
    /**
    * Provides access the Zencoder Outputs API
    *
    * Valid functions: details, progress
    *
    * @var Outputs
    */
    public $outputs;

    /**
    * Initialize the Services_Zencoder class and sub-classes.
    *
    * @param string               $api_key      API Key
    * @param string               $api_version  API version
    * @param string               $api_host     API host
    * @param bool                 $debug        Enable debug mode
    * @param string               $ca_path      Path to a directory that holds multiple CA certificates
    * @param string               $ca_file      Path to a file holding one or more certificates to verify the peer with
    * @param array                $curlOpts     Additional Curl Options
    */
    public function __construct(
        $api_key = NULL,
        $api_version = 'v2',
        $api_host = 'https://app.zencoder.com',
        $debug = false,
        $ca_path = NULL,
        $ca_file = NULL,
        $curlOpts = []
    )
    {
        // Check that library dependencies are met
        if (strnatcmp(phpversion(),'5.2.0') < 0) {
            throw new \Exception('PHP version 5.2 or higher is required.');
        }
        if (!function_exists('json_encode')) {
            throw new \Exception('JSON support must be enabled.');
        }
        if (!function_exists('curl_init')) {
            throw new \Exception('cURL extension must be enabled.');
        }

        $this->version = $api_version;

        $http_options = array("api_key" => $api_key, "debug" => $debug, "curlopts" => array_merge(array(CURLOPT_USERAGENT => self::USER_AGENT), $curlOpts));
        if (isset($ca_path)) {
          $http_options["curlopts"][CURLOPT_CAPATH] = realpath($ca_path);
        }
        if (isset($ca_file)) {
          $http_options["curlopts"][CURLOPT_CAINFO] = realpath($ca_file);
        }

        $this->http = new Http($api_host, $http_options);
        $this->accounts = new Accounts($this);
        $this->inputs = new Inputs($this);
        $this->jobs = new Jobs($this);
        $this->notifications = new Notifications($this);
        $this->outputs = new Outputs($this);

        parent::__construct($this);
    }

    /**
    * GET the resource at the specified path.
    *
    * @param string $path   Path to the resource
    * @param array  $params Query string parameters
    * @param array  $opts   Optional overrides
    *
    * @return object The object representation of the resource
    */
    public function retrieveData($path, array $params = array(), array $opts = array())
    {
        return empty($params)
            ? $this->_processResponse($this->http->get($this->_getApiPath($opts) . $path))
            : $this->_processResponse(
                $this->http->get($this->_getApiPath($opts) . $path . "?" . http_build_query($params, '', '&'))
            );
    }

    /**
    * DELETE the resource at the specified path.
    *
    * @param string $path   Path to the resource
    * @param array  $opts   Optional overrides
    *
    * @return object The object representation of the resource
    */
    public function deleteData($path, array $opts = array())
    {
        return $this->_processResponse($this->http->delete($this->_getApiPath($opts) . $path));
    }

    /**
    * POST to the resource at the specified path.
    *
    * @param string $path   Path to the resource
    * @param string $body   Raw body to post
    * @param array  $opts   Optional overrides
    *
    * @return object The object representation of the resource
    */
    public function createData($path, $body = "", array $opts = array())
    {
        $headers = array('Content-Type' => 'application/json');
        return empty($body)
            ? $this->_processResponse($this->http->post($this->_getApiPath($opts) . $path, $headers))
            : $this->_processResponse(
                $this->http->post(
                    $this->_getApiPath($opts) . $path,
                    $headers,
                    $body
                )
            );
    }

    /**
    * PUT to the resource at the specified path.
    *
    * @param string $path   Path to the resource
    * @param string $body   Raw body to post
    * @param array  $opts   Optional overrides
    *
    * @return object The object representation of the resource
    */
    public function updateData($path, $body = "", array $opts = array())
    {
        $headers = array('Content-Type' => 'application/json');
        return empty($params)
            ? $this->_processResponse($this->http->put($this->_getApiPath($opts) . $path, $headers))
            : $this->_processResponse(
                $this->http->put(
                    $this->_getApiPath($opts) . $path,
                    $headers,
                    $body
                )
            );
    }

    private function _getApiPath($opts = array())
    {
        return isset($opts['no_transform'])
            ? ""
            : "/api/" . (
                isset($opts['api_version'])
                ? $opts['api_version']
                : $this->version
            ) . "/";
    }

    private function _processResponse($response)
    {
        list($status, $headers, $body) = $response;
        if ( $status == 204 || (($status == 200 || $status == 201) && trim($body) == "")) {
            return TRUE;
        }
        if (empty($headers['Content-Type'])) {
            throw new \Exception('Response header is missing Content-Type', $body);
        }
        switch ($headers['Content-Type']) {
            case 'application/json':
            case 'application/json; charset=utf-8':
                return $this->_processJsonResponse($status, $headers, $body);
                break;
        }
        throw new \Exception(
            'Unexpected content type: ' . $headers['Content-Type']);
    }

    private function _processJsonResponse($status, $headers, $body)
    {
        $decoded = json_decode($body);
        if ($status >= 200 && $status < 300) {
            return $decoded;
        }
        throw new \Exception(
            "Invalid HTTP status code: " . $status
        );
    }
}
