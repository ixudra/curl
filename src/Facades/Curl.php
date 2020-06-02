<?php namespace Ixudra\Curl\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * @method static \Ixudra\Curl\Builder to(string $url)
 */
class Curl extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Curl';
    }

}
