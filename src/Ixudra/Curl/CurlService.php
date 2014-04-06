<?php namespace Ixudra\Curl;


class CurlService {

    public function get($url)
    {
        $curl = new Curl();
        $curl->setUrl( $url );

        $response = $curl->send();

        return $response;
    }

    public function post($url, $parameters)
    {
        $curl = new Curl();
        $curl->setUrl( $url );
        $curl->setMethod( true );
        $curl->setPostParameters( $parameters );

        $response = $curl->send();

        return $response;
    }

}