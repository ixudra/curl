<?php namespace Ixudra\Curl;


use stdClass;

class Builder {

    /** @var resource $curlObject       cURL request */
    protected $curlObject = null;

    /** @var array $curlOptions         Array of cURL options */
    protected $curlOptions = array(
        'RETURNTRANSFER'        => true,
        'FAILONERROR'           => false,
        'FOLLOWLOCATION'        => false,
        'CONNECTTIMEOUT'        => 30,
        'TIMEOUT'               => 30,
        'USERAGENT'             => '',
        'URL'                   => '',
        'POST'                  => false,
        'HTTPHEADER'            => array(),
        'SSL_VERIFYPEER'        => false,
        'NOBODY'                => false,
        'HEADER'                => false,
    );

    /** @var array $packageOptions      Array with options that are not specific to cURL but are used by the package */
    protected $packageOptions = array(
        'data'                  => array(),
        'files'                 => array(),
        'asJsonRequest'         => false,
        'asJsonResponse'        => false,
        'returnAsArray'         => false,
        'responseObject'        => false,
        'responseArray'         => false,
        'enableDebug'           => false,
        'xDebugSessionName'     => '',
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
     * @param   float $timeout    The timeout for the request (in seconds, fractions of a second are okay. Default: 30 seconds)
     * @return Builder
     */
    public function withTimeout($timeout = 30.0)
    {
        return $this->withCurlOption( 'TIMEOUT_MS', ($timeout * 1000) );
    }

    /**
     * Set the connect timeout
     *
     * @param   float $timeout    The connect timeout for the request (in seconds, fractions of a second are okay. Default: 30 seconds)
     * @return Builder
     */
    public function withConnectTimeout($timeout = 30.0)
    {
        return $this->withCurlOption( 'CONNECTTIMEOUT_MS', ($timeout * 1000) );
    }

    /**
     * Add GET or POST data to the request
     *
     * @param   mixed $data     Array of data that is to be sent along with the request
     * @return Builder
     */
    public function withData($data = array())
    {
        return $this->withPackageOption( 'data', $data );
    }

    /**
     * Add a file to the request
     *
     * @param   string $key          Identifier of the file (how it will be referenced by the server in the $_FILES array)
     * @param   string $path         Full path to the file you want to send
     * @param   string $mimeType     Mime type of the file
     * @param   string $postFileName Name of the file when sent. Defaults to file name
     *
     * @return Builder
     */
    public function withFile($key, $path, $mimeType = '', $postFileName = '')
    {
        $fileData = array(
            'fileName'     => $path,
            'mimeType'     => $mimeType,
            'postFileName' => $postFileName,
        );

        $this->packageOptions[ 'files' ][ $key ] = $fileData;

        return $this->containsFile();
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
     * @param   mixed  $value       The value to which the option is to be set
     * @return Builder
     */
    public function withOption($key, $value)
    {
        return $this->withCurlOption( $key, $value );
    }

    /**
     * Set Cookie File
     *
     * @param   string $cookieFile  File name to read cookies from
     * @return Builder
     */
    public function setCookieFile($cookieFile)
    {
        return $this->withOption( 'COOKIEFILE', $cookieFile );
    }

    /**
     * Set Cookie Jar
     *
     * @param   string $cookieJar   File name to store cookies to
     * @return Builder
     */
    public function setCookieJar($cookieJar)
    {
        return $this->withOption( 'COOKIEJAR', $cookieJar );
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
        $data = array();
        foreach( $headers as $key => $value ) {
            if( !is_numeric($key) ) {
                $value = $key .': '. $value;
            }

            $data[] = $value;
        }

        $this->curlOptions[ 'HTTPHEADER' ] = array_merge(
            $this->curlOptions[ 'HTTPHEADER' ], $data
        );

        return $this;
    }

    /**
     * Add an HTTP Authorization header to the request
     *
     * @param   string $token       The authorization token that is to be added to the request
     * @return Builder
     */
    public function withAuthorization($token)
    {
        return $this->withHeader( 'Authorization: ' . $token );
    }

    /**
     * Add a HTTP bearer authorization header to the request
     *
     * @param   string $bearer      The bearer token that is to be added to the request
     * @return Builder
     */
    public function withBearer($bearer)
    {
        return $this->withAuthorization(  'Bearer '. $bearer );
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
     * Add response headers to the response object or response array
     *
     * @return Builder
     */
    public function withResponseHeaders()
    {
        return $this->withCurlOption( 'HEADER', TRUE );
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
     * Return a full response array with HTTP status and headers instead of only the content
     *
     * @return Builder
     */
    public function returnResponseArray()
    {
        return $this->withPackageOption( 'responseArray', true );
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
            ->withOption( 'VERBOSE', true );
    }

    /**
     * Enable Proxy for the cURL request
     *
     * @param   string $proxy       Hostname
     * @param   string $port        Port to be used
     * @param   string $type        Scheme to be used by the proxy
     * @param   string $username    Authentication username
     * @param   string $password    Authentication password
     * @return Builder
     */
    public function withProxy($proxy, $port = '', $type = '', $username = '', $password = '')
    {
        $this->withOption( 'PROXY', $proxy );

        if( !empty($port) ) {
            $this->withOption( 'PROXYPORT', $port );
        }

        if( !empty($type) ) {
            $this->withOption( 'PROXYTYPE', $type );
        }

        if( !empty($username) && !empty($password) ) {
            $this->withOption( 'PROXYUSERPWD', $username .':'. $password );
        }

        return $this;
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
     * Add the XDebug session name to the request to allow for easy debugging
     *
     * @param  string $sessionName
     * @return Builder
     */
    public function enableXDebug($sessionName = 'session_1')
    {
        $this->packageOptions[ 'xDebugSessionName' ] = $sessionName;

        return $this;
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
         $this->appendDataToURL();
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
        if( !empty($this->packageOptions[ 'files' ]) ) {
            foreach( $this->packageOptions[ 'files' ] as $key => $file ) {
                $parameters[ $key ] = $this->getCurlFileValue( $file[ 'fileName' ], $file[ 'mimeType' ], $file[ 'postFileName'] );
            }
        }

        if( $this->packageOptions[ 'asJsonRequest' ] ) {
            $parameters = \json_encode($parameters);
        }

        $this->curlOptions[ 'POSTFIELDS' ] = $parameters;
    }

    protected function getCurlFileValue($filename, $mimeType, $postFileName)
    {
        // PHP 5 >= 5.5.0, PHP 7
        if( function_exists('curl_file_create') ) {
            return curl_file_create($filename, $mimeType, $postFileName);
        }

        // Use the old style if using an older version of PHP
        $value = "@{$filename};filename=" . $postFileName;
        if( $mimeType ) {
            $value .= ';type=' . $mimeType;
        }

        return $value;
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
        $this->setPostParameters();

        return $this->withOption('CUSTOMREQUEST', 'DELETE')
            ->send();
    }

    /**
     * Send a HEAD request to a URL using the specified cURL options
     *
     * @return mixed
     */
    public function head()
    {
        $this->appendDataToURL();
        $this->withCurlOption('NOBODY', true);
        $this->withCurlOption('HEADER', true);

        return $this->send();
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

        $responseHeader = null;
        if( $this->curlOptions[ 'HEADER' ] ) {
            $headerSize = curl_getinfo( $this->curlObject, CURLINFO_HEADER_SIZE );
            $responseHeader = substr( $response, 0, $headerSize );
            $response = substr( $response, $headerSize );
        }

        // Capture additional request information if needed
        $responseData = array();
        if( $this->packageOptions[ 'responseObject' ] || $this->packageOptions[ 'responseArray' ] ) {
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
        return $this->returnResponse( $response, $responseData, $responseHeader );
    }

    /**
     * @param   string $headerString    Response header string
     * @return mixed
     */
    protected function parseHeaders($headerString)
    {
        $headers = array_filter(array_map(function ($x) {
            $arr = array_map('trim', explode(':', $x, 2));
            if( count($arr) == 2 ) {
                return [ $arr[ 0 ] => $arr[ 1 ] ];
            }
        }, array_filter(array_map('trim', explode("\r\n", $headerString)))));

        $results = array();

        foreach( $headers as $values ) {
            if( !is_array($values) ) {
                continue;
            }

            $key = array_keys($values)[ 0 ];
            if( isset($results[ $key ]) ) {
                $results[ $key ] = array_merge(
                    (array) $results[ $key ],
                    array( array_values($values)[ 0 ] )
                );
            } else {
                $results = array_merge(
                    $results,
                    $values
                );
            }
        }

        return $results;
    }

    /**
     * @param   mixed $content          Content of the request
     * @param   array $responseData     Additional response information
     * @param   string $header          Response header string
     * @return mixed
     */
    protected function returnResponse($content, array $responseData = array(), $header = null)
    {
        if( !$this->packageOptions[ 'responseObject' ] && !$this->packageOptions[ 'responseArray' ] ) {
            return $content;
        }

        $object = new stdClass();
        $object->content = $content;
        $object->status = $responseData[ 'http_code' ];
        $object->contentType = $responseData[ 'content_type' ];
        if( array_key_exists('errorMessage', $responseData) ) {
            $object->error = $responseData[ 'errorMessage' ];
        }

        if( $this->curlOptions[ 'HEADER' ] ) {
            $object->headers = $this->parseHeaders( $header );
        }

        if( $this->packageOptions[ 'responseObject' ] ) {
            return $object;
        }

        if( $this->packageOptions[ 'responseArray' ] ) {
            return (array) $object;
        }

        return $content;
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

            if( !$this->packageOptions[ 'containsFile' ] && $key === 'POSTFIELDS' && is_array( $value ) ) {
                $results[ $arrayKey ] = http_build_query( $value );
            } else {
                $results[ $arrayKey ] = $value;
            }
        }

        if( !empty($this->packageOptions[ 'xDebugSessionName' ]) ) {
            $char = strpos($this->curlOptions[ 'URL' ], '?') ? '&' : '?';
            $this->curlOptions[ 'URL' ] .= $char . 'XDEBUG_SESSION_START='. $this->packageOptions[ 'xDebugSessionName' ];
        }

        return $results;
    }

    /**
     * Append set data to the query string for GET, HEAD and DELETE cURL requests
     *
     * @return string
     */
    protected function appendDataToURL()
    {
        $parameterString = '';
        if( is_array($this->packageOptions[ 'data' ]) && count($this->packageOptions[ 'data' ]) != 0 ) {
            $parameterString = '?'. http_build_query( $this->packageOptions[ 'data' ] );
        }

        return $this->curlOptions[ 'URL' ] .= $parameterString;
    }

}
