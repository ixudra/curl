<?php namespace Ixudra\Curl;


class Curl {

    protected $curlObject = null;

    protected $options = array(
        'RETURN_TRANSFER'       => true,
        'FAIL_ON_ERROR'         => true,
        'FOLLOW_LOCATION'       => false,
        'CONNECT_TIMEOUT'       => '',
        'TIMEOUT'               => 30,
        'USER_AGENT'            => '',
        'URL'                   => '',
        'POST'                  => false,
        'HTTP_HEADER'           => array()
    );


    public function send()
    {
        $this->curlObject = curl_init();
        $options = $this->forgeOptions();
        curl_setopt_array( $this->curlObject, $options );

        $response = curl_exec( $this->curlObject );
        curl_close( $this->curlObject );

        return $response;
    }

    public function setUrl($baseUrl, $getParameters = array())
    {
        $parameterString = '';
        if( is_array($getParameters) && count($getParameters) != 0 ) {
            $parameterString = '?'. http_build_query($getParameters);
        }

        return $this->options[ 'URL' ] = $baseUrl . $parameterString;
    }

    public function setMethod($method)
    {
        $this->options[ 'POST' ] = $method;
    }

    public function setPostParameters($parameters)
    {
        $this->options[ 'POST' ] = true;
        $this->options[ 'POST_FIELDS' ] = $parameters;
    }

    public function addOption($key, $value)
    {
        $this->options[ $key ] = $value;
    }

    protected function forgeOptions()
    {
        $results = array();
        foreach( $this->options as $key => $value ) {
            $array_key = constant( 'CURLOPT_' . str_replace('_', '', $key) );

            if( $key == 'POST_FIELDS' && is_array( $value ) ) {
                $results[ $array_key ] = http_build_query( $value, null, '&' );
            } else {
                $results[ $array_key ] = $value;
            }
        }

        return $results;
    }

}