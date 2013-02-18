<?php

namespace Adagio\DrawSomething\Transport
{

use Adagio\DrawSomething\Exception\TransportException;

class Curl // implements Psr\Http\Client\HttpClient
{
    /**
     * @var resource
     */
    protected $handle;

    protected $log;

    public function __construct()
    {
        $this->handle = curl_init();

        $this->log = fopen('php://memory', 'a+');

        curl_setopt_array(
            $this->handle,
            array(
                CURLOPT_RETURNTRANSFER    => true,
                CURLOPT_HEADER            => true,
                CURLOPT_MAXREDIRS         => 5,
                CURLOPT_TIMEOUT_MS        => 5000,
                CURLOPT_CONNECTTIMEOUT_MS => 5000,
                CURLOPT_VERBOSE           => true,
//                CURLOPT_STDERR            => $this->log,
            )
        );
    }

    public function __destruct()
    {
        fclose($this->log);
        if (is_resource($this->handle)) {
            curl_close($this->handle);
        }
    }

    /**
     *
     * @param string $method
     * @param string $url
     * @param string|array $content
     * @param array $headers
     * @param array $options
     * @return Response
     */
    public function request($method, $url, $content = null, array $headers = array(), array $options = array())
    {
        curl_setopt($this->handle, CURLOPT_URL, $url);

        if ('POST' == $method) {
            curl_setopt($this->handle, CURLOPT_POST, true);
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, is_array($content) ? http_build_query($content) : $content);
        } else {
            curl_setopt($this->handle, CURLOPT_POST, false);
        }

        $response = curl_exec($this->handle);
        if ($response === false || strlen($response) < 1) {
            throw new TransportException(curl_error($this->handle));
        }

        $header_size = curl_getinfo($this->handle, CURLINFO_HEADER_SIZE);

        return new Response(
                curl_getinfo($this->handle, CURLINFO_HTTP_CODE),
                curl_getinfo($this->handle, CURLINFO_CONTENT_TYPE),
                substr($response, curl_getinfo($this->handle, CURLINFO_HEADER_SIZE)),
                http_parse_headers($response, 0, $header_size));
    }
}

}

namespace
{
    if (!function_exists('http_parse_headers')) {
        function http_parse_headers( $header )
        {
            $retVal = array();
            $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
            foreach( $fields as $field ) {
                if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
                    $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                    if( isset($retVal[$match[1]]) ) {
                        if (!is_array($retVal[$match[1]])) {
                            $retVal[$match[1]] = array($retVal[$match[1]]);
                        }
                        $retVal[$match[1]][] = $match[2];
                    } else {
                        $retVal[$match[1]] = trim($match[2]);
                    }
                }
            }
            return $retVal;
        }
    }
}