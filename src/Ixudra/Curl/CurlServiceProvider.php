<?php namespace Ixudra\Curl;


use Illuminate\Support\ServiceProvider;

class CurlServiceProvider extends ServiceProvider {

    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @return void
     */
    public function register()
    {
        $this->app['Curl'] = $this->app->share(
            function($app)
            {
                return new CurlService();
            }
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return array('Curl');
    }

}