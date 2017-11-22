<?php

namespace Zencoder\Services\Zencoder;

class Http extends Base
{
    protected $api_key;
    protected $scheme;
    protected $host;
    protected $debug;
    protected $curlopts;

    public function __construct($uri = '', $kwargs = [])
    {
        foreach (parse_url($uri) as $name => $value) {
            $this->$name = $value;
        }
        $this->api_key = isset($kwargs['api_key']) ? $kwargs['api_key'] : null;
        $this->debug = isset($kwargs['debug']) ? (bool) $kwargs['debug'] : null;
        $this->curlopts = isset($kwargs['curlopts']) ? $kwargs['curlopts'] : [];
        parent::__construct($this);
    }

    public function __call($name, $args)
    {
        list($res, $req_headers, $req_body) = $args + [0, [], ''];

        $opts = $this->curlopts + [
            CURLOPT_URL => "$this->scheme://$this->host$res",
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_INFILESIZE => -1,
            CURLOPT_POSTFIELDS => null,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];

        foreach ($req_headers as $k => $v) {
            $opts[CURLOPT_HTTPHEADER][] = "$k: $v";
        }
        if ($this->debug) {
            $opts[CURLINFO_HEADER_OUT] = true;
        }
        if ($this->api_key) {
            $opts[CURLOPT_HTTPHEADER][] = "Zencoder-Api-Key: $this->api_key";
        }
        switch ($name) {
            case 'get':
              $opts[CURLOPT_HTTPGET] = true;

              break;
            case 'post':
              $opts[CURLOPT_POST] = true;
              $opts[CURLOPT_POSTFIELDS] = $req_body;

              break;
            case 'put':
              $opts[CURLOPT_PUT] = true;
              if (strlen($req_body)) {
                  if ($buf = fopen('php://memory', 'w+')) {
                      fwrite($buf, $req_body);
                      fseek($buf, 0);
                      $opts[CURLOPT_INFILE] = $buf;
                      $opts[CURLOPT_INFILESIZE] = strlen($req_body);
                  } else {
                      throw new HttpException('Unable to open memory buffer');
                  }
              } else {
                  $opts[CURLOPT_INFILESIZE] = 0;
              }

              break;
            case 'delete':
              $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';

              break;
            default:
              throw new HttpException('Invalid HTTP Method');
              break;
        }

        try {
            if ($curl = curl_init()) {
                if (curl_setopt_array($curl, $opts)) {
                    if ($response = curl_exec($curl)) {
                        $parts = explode("\r\n\r\n", $response, 3);
                        list($head, $body) = ($parts[0] === 'HTTP/1.1 100 Continue' || $parts[0] = 'HTTP/1.1 200 Connection established') ? [$parts[1], $parts[2]] : [$parts[0], $parts[1]];
                        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        if ($this->debug) {
                            error_log(curl_getinfo($curl, CURLINFO_HEADER_OUT).$req_body);
                        }

                        $header_lines = explode("\r\n", $head);
                        array_shift($header_lines);
                        $headers = [];

                        foreach ($header_lines as $line) {
                            list($key, $value) = explode(':', $line, 2);
                            $headers[$key] = trim($value);
                        }

                        curl_close($curl);

                        if (isset($buf) && is_resource($buf)) {
                            fclose($buf);
                        }

                        return [$status, $headers, $body];
                    }

                    throw new HttpException(curl_error($curl));
                }

                throw new HttpException(curl_error($curl));
            }

            throw new HttpException('Unable to initialize cURL');
        } catch (HttpException $e) {
            if (isset($curl) && is_resource($curl)) {
                curl_close($curl);
            }
            if (isset($buf) && is_resource($buf)) {
                fclose($buf);
            }

            throw $e;
        }
    }
}
