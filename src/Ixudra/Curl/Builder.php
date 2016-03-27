<?php namespace Ixudra\Curl;


class Builder {

    /** @var resource $curlObject       cURL request */
    protected $curlObject = null;

    /** @var array $curlOptions         Array of cURL options */
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

    /** @var array $packageOptions      Array with options that are not specific to cURL but are used by the package */
    protected $packageOptions = array(
        'data'                  => array(),
        'asJson'                => false,
        'returnAsArray'         => false,
        'saveFile'              => '',
    );


    /**
     * Set the URL to which the request is to be sent
     *
     * @param $url string   The URL to which the request is to be sent
     * @return Builder
     */
    public function to($url)
    {
        return $this->withCurlOption( 'URL', $url );
    }

    /**
     * Set the request timeout
     *
     * @param   integer $timeout    The timeout for the request (in seconds. Default: 30 seconds)
     * @return Builder
     */
    public function withTimeout($timeout = 30)
    {
        return $this->withCurlOption( 'TIMEOUT', $timeout );
    }

    /**
     * Add GET or POST data to the request
     *
     * @param   array $data     Array of data that is to be sent along with the request
     * @return Builder
     */
    public function withData($data = array())
    {
        return $this->withPackageOption( 'data', $data );
    }

    /**
     * Configure the package to encode and decode the request data
     *
     * @param   boolean $asArray    Indicates whether or not the data should be returned as an array. Default: false
     * @return Builder
     */
    public function asJson($asArray = false)
    {
        return $this->withPackageOption( 'asJson', true )
            ->withPackageOption( 'returnAsArray', $asArray );
    }

//    /**
//     * Send the request over a secure connection
//     *
//     * @return Builder
//     */
//    public function secure()
//    {
//        return $this;
//    }

    /**
     * Set any specific cURL option
     *
     * @param   string $key         The name of the cURL option
     * @param   string $value       The value to which the option is to be set
     * @return Builder
     */
    public function withOption($key, $value)
    {
        return $this->withCurlOption( $key, $value );
    }

    /**
     * Set any specific cURL option
     *
     * @param   string $key         The name of the cURL option
     * @param   string $value       The value to which the option is to be set
     * @return Builder
     */
    protected function withCurlOption($key, $value)
    {
        $this->curlOptions[ $key ] = $value;

        return $this;
    }

    /**
     * Set any specific package option
     *
     * @param   string $key       The name of the cURL option
     * @param   string $value     The value to which the option is to be set
     * @return Builder
     */
    protected function withPackageOption($key, $value)
    {
        $this->packageOptions[ $key ] = $value;

        return $this;
    }

    /**
     * Add a HTTP header to the request
     *
     * @param   string $header      The HTTP header that is to be added to the request
     * @return Builder
     */
    public function withHeader($header)
    {
        $this->curlOptions[ 'HTTPHEADER' ][] = $header;

        return $this;
    }

    /**
     * Add a content type HTTP header to the request
     *
     * @param   string $contentType    The content type of the file you would like to download
     * @return Builder
     */
    public function withContentType($contentType)
    {
        return $this->withHeader( 'Content-Type: '. $contentType )
            ->withHeader( 'Connection: Keep-Alive' );
    }

    /**
     * Send a GET request to a URL using the specified cURL options
     *
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
     * Send a POST request to a URL using the specified cURL options
     *
     * @return mixed
     */
    public function post()
    {
        $this->setPostParameters();

        return $this->send();
    }

     /**
      * Send a download request to a URL using the specified cURL options
      *
      * @param  string $fileName
      * @return mixed
      */
     public function download($fileName)
     {
         $this->packageOptions[ 'saveFile' ] = $fileName;

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
//     * Send a PUT request to a URL using the specified cURL options
//     *
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
//     * Send a DELETE request to a URL using the specified cURL options
//     *
//     * @return mixed
//     */
//    public function delete()
//    {
//        $this->setPostParameters();
//
//        return $this->send();
//    }

    /**
     * Send the request
     *
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

        if( $this->packageOptions[ 'saveFile' ] ) {
            // Save to file if a filename was specified
            $file = fopen($this->packageOptions[ 'saveFile' ], 'w');
            fwrite($file, $response);
            fclose($file);
        } else if( $this->packageOptions[ 'asJson' ] ) {
            // Decode the request if necessary
            $response = json_decode($response, $this->packageOptions[ 'returnAsArray' ]);
        }

        // Return the result
        return $response;
    }

    /**
     * Convert the curlOptions to an array of usable options for the cURL request
     *
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
