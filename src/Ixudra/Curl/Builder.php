<?php namespace Ixudra\Curl;


class Builder {

    protected $curlObject = null;

    protected $curlOptions = array(
        'RETURNTRANSFER'        => true,
        'FAILONERROR'           => true,
        'FOLLOWLOCATION'        => false,
        'CONNECTTIMEOUT'        => '',
        'TIMEOUT'               => 30,
        'USERAGENT'             => '',
        'URL'                   => '',
        'POST'                  => false,
        'HTTPHEADER'            => array(),
    );

    protected $packageOptions = array(
        'data'                  => array(),
        'asJson'                => false,
    );


    /**
     *  Set the URL to which the request is to be sent
     * @param $url string   The URL to which the request is to be sent
     * @return $this
     */
    public function to($url)
    {
        return $this->withCurlOption( 'URL', $url );
    }

    /**
     *  Set the request timeout
     * @param $timeout integer  The timeout for the request (in seconds. Default: 30 seconds)
     * @return $this
     */
    public function withTimeout($timeout = 30)
    {
        return $this->withCurlOption( 'TIMEOUT', $timeout );
    }

    /**
     *  Add GET or POST data to the request
     * @param $data array   Array of data that is to be sent along wiht the request
     * @return $this
     */
    public function withData($data = array())
    {
        return $this->withPackageOption( 'data', $data );
    }

    /**
     *  Configure the package to encode and decode the request data
     * @return $this
     */
    public function asJson()
    {
        return $this->withPackageOption( 'asJson', true );
    }

//    /**
//     *  Send the request over a secure connection
//     * @return $this
//     */
//    public function secure()
//    {
//        return $this;
//    }

    /**
     *  Set any specific cURL option
     * @param $key string       The name of the cURL option
     * @param $value string     The value to which the option is to be set
     * @return $this
     */
    public function withOption($key, $value)
    {
        return $this->withCurlOption( $key, $value );
    }

    /**
     *  Set any specific cURL option
     * @param $key string       The name of the cURL option
     * @param $value string     The value to which the option is to be set
     * @return $this
     */
    protected function withCurlOption($key, $value)
    {
        $this->curlOptions[ $key ] = $value;

        return $this;
    }

    /**
     *  Set any specific package option
     * @param $key string       The name of the cURL option
     * @param $value string     The value to which the option is to be set
     * @return $this
     */
    protected function withPackageOption($key, $value)
    {
        $this->packageOptions[ $key ] = $value;

        return $this;
    }

    /**
     *  Add a HTTP header to the request
     * @param $header string    The HTTP header that is to be added to the request
     * @return $this
     */
    public function withHeader($header)
    {
        $this->curlOptions[ 'HTTPHEADER' ][] = $header;

        return $this;
    }

    /**
     *  Send a GET request to a URL using the specified cURL options
     * @return mixed
     */
    public function get()
    {
        $parameterString = '';
        if( is_array($this->packageOptions[ 'data' ]) && count($this->packageOptions[ 'data' ]) != 0 ) {
            $parameterString = '?'. http_build_query($this->packageOptions[ 'data' ]);
        }

        $this->curlOptions[ 'URL' ] .= $parameterString;

        return $this->send();
    }

    /**
     *  Send a POST request to a URL using the specified cURL options
     * @return mixed
     */
    public function post()
    {
        $this->setPostParameters();

        return $this->send();
    }

    /**
     * Add POST parameters to the curlOptions array
     */
    protected function setPostParameters()
    {
        $this->curlOptions[ 'POST' ] = true;

        $parameters = $this->packageOptions[ 'data' ];
        if( $this->packageOptions[ 'asJson' ] ) {
            $parameters = json_encode($parameters);
        }

        $this->curlOptions[ 'POSTFIELDS' ] = $parameters;
    }

//    /**
//     *  Send a PUT request to a URL using the specified cURL options
//     * @return mixed
//     */
//    public function put()
//    {
//        $this->setPostParameters();
//
//        return $this->send();
//    }
//
//    /**
//     *  Send a DELETE request to a URL using the specified cURL options
//     * @return mixed
//     */
//    public function delete()
//    {
//        $this->setPostParameters();
//
//        return $this->send();
//    }

    /**
     *  Send the request
     * @return mixed
     */
    protected function send()
    {
        // Add JSON header if necessary
        if( $this->packageOptions[ 'asJson' ] ) {
            $this->withHeader( 'Content-Type: application/json' );
        }

        // Create the request with all specified options
        $this->curlObject = curl_init();
        $options = $this->forgeOptions();
        curl_setopt_array( $this->curlObject, $options );

        // Send the request
        $response = curl_exec( $this->curlObject );
        curl_close( $this->curlObject );

        // Decode the request if necessary
        if( $this->packageOptions[ 'asJson' ] ) {
            $response = json_decode($response);
        }

        // Return the result
        return $response;
    }

    /**
     *  Convert the curlOptions to an array of usable options for the cURL request
     * @return array
     */
    protected function forgeOptions()
    {
        $results = array();
        foreach( $this->curlOptions as $key => $value ) {
            $array_key = constant( 'CURLOPT_' . $key );

            if( $key == 'POSTFIELDS' && is_array( $value ) ) {
                $results[ $array_key ] = http_build_query( $value, null, '&' );
            } else {
                $results[ $array_key ] = $value;
            }
        }

        return $results;
    }

}
