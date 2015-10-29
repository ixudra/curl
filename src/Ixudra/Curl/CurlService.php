<?php namespace Ixudra\Curl;


class CurlService {

    public function get($url, $getParameters = array(), $isJson = false, $curlOptions = array())
    {
        $curl = new Curl();
        $curl->setUrl( $url, $getParameters );

        foreach( $curlOptions as $key => $value ) {
            $curl->addOption( $key, $value );
        }

        return $this->send( $curl, $isJson );
    }

    public function post($url, $getParameters = array(), $postParameters, $isJson = false, $curlOptions = array())
    {
        $curl = new Curl();
        $curl->setUrl( $url, $getParameters );
        $curl->setMethod( true );

        if( $isJson ) {
            $postParameters = json_encode($postParameters);
            $curl->addOption( 'HTTP_HEADER', array('Content-Type: application/json') );
        }

        $curl->setPostParameters( $postParameters );

        foreach( $curlOptions as $key => $value ) {
            $curl->addOption( $key, $value );
        }

        return $this->send( $curl, $isJson );
    }

    protected function send($curl, $isJson)
    {
        $response = $curl->send();
        if( $isJson ) {
            $response = json_decode($response);
        }

        return $response;
    }

    public function to($url)
    {
        $builder = new Builder();

        return $builder->to($url);
    }

}