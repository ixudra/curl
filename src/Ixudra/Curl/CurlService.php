<?php namespace Ixudra\Curl;


class CurlService {

    /**
     * @param $url
     * @param array $getParameters
     * @param bool $isJson
     * @param array $curlOptions
     * @return mixed
     * @deprecated
     */
    public function get($url, $getParameters = array(), $isJson = false, $curlOptions = array())
    {
        $curl = new Curl();
        $curl->setUrl( $url, $getParameters );

        foreach( $curlOptions as $key => $value ) {
            $curl->addOption( $key, $value );
        }

        return $this->send( $curl, $isJson );
    }

    /**
     * @param $url
     * @param array $getParameters
     * @param $postParameters
     * @param bool $isJson
     * @param array $curlOptions
     * @return mixed
     * @deprecated
     */
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

    /**
     * @param $curl
     * @param $isJson
     * @return mixed
     */
    protected function send($curl, $isJson)
    {
        $response = $curl->send();
        if( $isJson ) {
            $response = json_decode($response);
        }

        return $response;
    }

    /**
     * @param $url string   The URL to which the request is to be sent
     * @return \Ixudra\Curl\Builder
     */
    public function to($url)
    {
        $builder = new Builder();

        return $builder->to($url);
    }

}