<?php namespace Ixudra\Curl;


class Curl {

    protected $_curlObject = null;

    protected $_options = array(
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
        $this->_curlObject = curl_init();
        $options = $this->_forgeOptions();
        curl_setopt_array( $this->_curlObject, $options );

        $response = curl_exec($this->_curlObject);
        curl_close( $this->_curlObject );

        return $response;
    }

    public function setUrl($url)
    {
        $this->_options['URL'] = $url;
    }

    public function setMethod($method)
    {
        $this->_options['POST'] = $method;
    }

    public function setPostParameters(array $parameters)
    {
        $this->_options['POST'] = true;
        $this->_options['POST_FIELDS'] = $parameters;
    }

    protected function _forgeOptions()
    {
        $results = array();
        foreach( $this->_options as $key => $value ) {
            $array_key = constant( 'CURLOPT_' . str_replace('_', '', $key) );

            if( $key == 'POST_FIELDS' && is_array($value) ) {
                $results[$array_key] = http_build_query($value, NULL, '&');
            } else {
                $results[$array_key] = $value;
            }
        }

        return $results;
    }

}