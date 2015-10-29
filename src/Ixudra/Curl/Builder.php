<?php namespace Ixudra\Curl;


class Builder {

    protected $curlObject = null;

    protected $curlOptions = array(
        'RETURN_TRANSFER'       => true,
        'FAIL_ON_ERROR'         => true,
        'FOLLOW_LOCATION'       => false,
        'CONNECT_TIMEOUT'       => '',
        'TIMEOUT'               => 30,
        'USER_AGENT'            => '',
        'URL'                   => '',
        'POST'                  => false,
        'HTTP_HEADER'           => array(),
    );

    protected $packageOptions = array(
        'url'                   => '',
        'parameters'            => array(),
        'asJson'                => false,
    );


    /**
     *  Set the URL to which the request is to be sent
     * @return $this
     */
    public function to($url)
    {
        return $this;
    }

    /**
     *  Set the request timeout (default 30 seconds)
     * @return $this
     */
    public function withTimeout($timeout = 30)
    {
        return $this;
    }

    /**
     *  Configure the package to encode and decode the request data
     * @return $this
     */
    public function asJson()
    {
        return $this;
    }

    /**
     *  Send the request over a secure connection
     * @return $this
     */
    public function secure()
    {
        return $this;
    }

    /**
     *  Set any specific cURL option
     * @param $key string       The name of the cURL option
     * @param $value string     The value to which the option is to be set
     * @return $this
     */
    public function withOption($key, $value)
    {
        return $this;
    }

    /**
     *  Send a GET request to a URL using the specified cURL options
     */
    public function get()
    {
        return null;
    }

    /**
     *  Send a POST request to a URL using the specified cURL options
     */
    public function post()
    {
        return null;
    }

    /**
     *  Send a PUT request to a URL using the specified cURL options
     */
    public function put()
    {
        return null;
    }

    /**
     *  Send a DELETE request to a URL using the specified cURL options
     */
    public function delete()
    {
        return null;
    }

}