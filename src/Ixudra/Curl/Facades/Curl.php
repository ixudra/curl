<?php namespace Ixudra\Curl\Facades;


use Illuminate\Support\Facades\Facade;

class Curl extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Curl';
    }

}