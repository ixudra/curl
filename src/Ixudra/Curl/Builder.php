<?php namespace Ixudra\Curl;


use stdClass;

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
        'asJsonRequest'         => false,
        'asJsonResponse'        => false,
        'returnAsArray'         => false,
        'responseObject'        => false,
        'enableDebug'           => false,
        'containsFile'          => false,
        'debugFile'             => '',
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
     * Allow for redirects in the request
     *
     * @return Builder
     */
    public function allowRedirect()
    {
        return $this->withCurlOption( 'FOLLOWLOCATION', true );
    }

    /**
     * Configure the package to encode and decode the request data
     *
     * @param   boolean $asArray    Indicates whether or not the data should be returned as an array. Default: false
     * @return Builder
     */
    public function asJson($asArray = false)
    {
        return $this->asJsonRequest()
            ->asJsonResponse( $asArray );
    }

    /**
     * Configure the package to encode the request data to json before sending it to the server
     *
     * @return Builder
     */
    public function asJsonRequest()
    {
        return $this->withPackageOption( 'asJsonRequest', true );
    }

    /**
     * Configure the package to decode the request data from json to object or associative array
     *
     * @param   boolean $asArray    Indicates whether or not the data should be returned as an array. Default: false
     * @return Builder
     */
    public function asJsonResponse($asArray = false)
    {
        return $this->withPackageOption( 'asJsonResponse', true )
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
     * Add multiple HTTP header at the same time to the request
     *
     * @param   array $headers      Array of HTTP headers that must be added to the request
     * @return Builder
     */
    public function withHeaders(array $headers)
    {
        $this->curlOptions[ 'HTTPHEADER' ] = array_merge(
            $this->curlOptions[ 'HTTPHEADER' ], $headers
        );

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
     * Return a full response object with HTTP status and headers instead of only the content
     *
     * @return Builder
     */
    public function returnResponseObject()
    {
        return $this->withPackageOption( 'responseObject', true );
    }

    /**
     * Enable debug mode for the cURL request
     *
     * @param   string $logFile    The full path to the log file you want to use
     * @return Builder
     */
    public function enableDebug($logFile)
    {
        return $this->withPackageOption( 'enableDebug', true )
            ->withPackageOption( 'debugFile', $logFile )
            ->withOption('VERBOSE', true);
    }

    /**
     * Enable File sending
     *
     * @return Builder
     */
    public function containsFile()
    {
        return $this->withPackageOption( 'containsFile', true );
    }

    /**
     * Send a GET request to a URL using the specified cURL options
     *
     * @return mixed
     */
    public function get()
    {
        $this->appendDataToURL();

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
        if( $this->packageOptions[ 'asJsonRequest' ] ) {
            $parameters = json_encode($parameters);
        }

        $this->curlOptions[ 'POSTFIELDS' ] = $parameters;
    }

    /**
     * Send a PUT request to a URL using the specified cURL options
     *
     * @return mixed
     */
    public function put()
    {
        $this->setPostParameters();

        return $this->withOption('CUSTOMREQUEST', 'PUT')
            ->send();
    }

    /**
     * Send a PATCH request to a URL using the specified cURL options
     *
     * @return mixed
     */
    public function patch()
    {
        $this->setPostParameters();

        return $this->withOption('CUSTOMREQUEST', 'PATCH')
            ->send();
    }

    /**
     * Send a DELETE request to a URL using the specified cURL options
     *
     * @return mixed
     */
    public function delete()
    {
        $this->appendDataToURL();

        return $this->withOption('CUSTOMREQUEST', 'DELETE')
            ->send();
    }

    /**
     * Send the request
     *
     * @return mixed
     */
    protected function send()
    {
        // Add JSON header if necessary
        if( $this->packageOptions[ 'asJsonRequest' ] ) {
            $this->withHeader( 'Content-Type: application/json' );
        }

        if( $this->packageOptions[ 'enableDebug' ] ) {
            $debugFile = fopen( $this->packageOptions[ 'debugFile' ], 'w');
            $this->withOption('STDERR', $debugFile);
        }

        // Create the request with all specified options
        $this->curlObject = curl_init();
        $options = $this->forgeOptions();
        curl_setopt_array( $this->curlObject, $options );

        // Send the request
        $response = curl_exec( $this->curlObject );

        // Capture additional request information if needed
        $responseData = array();
        if( $this->packageOptions[ 'responseObject' ] ) {
            $responseData = curl_getinfo( $this->curlObject );

            if( curl_errno($this->curlObject) ) {
                $responseData[ 'errorMessage' ] = curl_error($this->curlObject);
            }
        }

        curl_close( $this->curlObject );

        if( $this->packageOptions[ 'saveFile' ] ) {
            // Save to file if a filename was specified
            $file = fopen($this->packageOptions[ 'saveFile' ], 'w');
            fwrite($file, $response);
            fclose($file);
        } else if( $this->packageOptions[ 'asJsonResponse' ] ) {
            // Decode the request if necessary
            $response = json_decode($response, $this->packageOptions[ 'returnAsArray' ]);
        }

        if( $this->packageOptions[ 'enableDebug' ] ) {
            fclose( $debugFile );
        }

        // Return the result
        return $this->returnResponse( $response, $responseData );
    }

    /**
     * @param   mixed $content          Content of the request
     * @param   array $responseData     Additional response information
     * @return stdClass
     */
    protected function returnResponse($content, $responseData = array())
    {
        if( !$this->packageOptions[ 'responseObject' ] ) {
            return $content;
        }

        $object = new stdClass();
        $object->content = $content;
        $object->status = $responseData[ 'http_code' ];
        if( array_key_exists('errorMessage', $responseData) ) {
            $object->error = $responseData[ 'errorMessage' ];
        }

        return $object;
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
            $arrayKey = constant( 'CURLOPT_' . $key );

            if( !$this->packageOptions[ 'containsFile' ] && $key == 'POSTFIELDS' && is_array( $value ) ) {
                $results[ $arrayKey ] = http_build_query( $value, null, '&' );
            } else {
                $results[ $arrayKey ] = $value;
            }
        }

        return $results;
    }

    /**
     * Append set data to the query string for GET and DELETE cURL requests
     *
     * @return string
     */
    protected function appendDataToURL()
    {
        $parameterString = '';
        if( is_array($this->packageOptions[ 'data' ]) && count($this->packageOptions[ 'data' ]) != 0 ) {
            $parameterString = '?'. http_build_query($this->packageOptions[ 'data' ]);
        }

        return $this->curlOptions[ 'URL' ] .= $parameterString;
    }

}
