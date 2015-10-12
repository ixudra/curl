ixudra/curl
================

Custom PHP curl library for the Laravel 5 framework - developed by [Ixudra](http://ixudra.be).

This package can be used by anyone at any given time, but keep in mind that it is optimized for my personal custom workflow. It may not suit your project perfectly and modifications may be in order.



## Installation

Pull this package in through Composer.

```js

    {
        "require": {
            "ixudra/curl": "5.*"
        }
    }

```

Add the service provider to your `config/app.php` file:

```php

    'providers'     => array(

        //...
        'Ixudra\Curl\CurlServiceProvider',

    ),

```

Add the facade to your `config/app.php` file:

```php

    'facades'       => array(

        //...
        'Curl'          => 'Ixudra\Curl\Facades\Curl',

    ),

```



## Usage

### GET requests

The package provides an easy interface for sending CURL requests from your application. Optionally, you can also 
include several `GET` parameters that will automatically be added to the base URL by the package automatically. Lastly, 
the package also has a parameter that allows you to easily mark a request as a JSON requests. The package will 
automatically handle the conversion from and to JSON to PHP if needed. The default value of this parameter is `false`. 
The last parameter can be used to pass additional CURL parameters to the request:

```php

    // Send a GET request to: http://www.foo.com/bar
    Curl::get('http://www.foo.com/bar');

    // Send a GET request to: http://www.foo.com/bar?foz=baz
    Curl::get('http://www.foo.com/bar', array('foz' => 'baz'));

    // Send a GET request to: http://www.foo.com/bar?foz=baz using JSON
    Curl::get('http://www.foo.com/bar', array('foz' => 'baz'), true);

    // Send a GET request to: http://www.foo.com/bar?foz=baz using JSON over SSL
    Curl::get('http://www.foo.com/bar', array('foz' => 'baz'), true, array('SSL_VERIFYPEER' => false));

```

The package will automatically prepend the options with the `CURLOPT_` prefix. It is worth noting that the package does 
not perform any validation on the CURL options. Additional information about available CURL options can be found
[here](http://php.net/manual/en/function.curl-setopt.php).



### POST requests

The package also allows you to send `POST` requests for your application. The first and second parameter are 
identical to the `Curl::get()` method. The `POST` parameters can be passed on as the third parameter. The fourth
parameter can be used to mark the request as a JSON requests. The package will automatically handle the conversion 
from and to JSON to PHP is needed. The default value of this parameter is `false`. The last parameter can be used to 
pass additional CURL parameters to the request:

```php

    // Send a POST request to: http://www.foo.com/bar with arguments 'fow' = 'baw'
    Curl::post('http://www.foo.com/bar', array(), array('fow' => 'baw'));

    // Send a POST request to: http://www.foo.com/bar?foz=baz with arguments 'fow' = 'baw'
    Curl::post('http://www.foo.com/bar', array('foz' => 'baz'), array('fow' => 'baw'));

    // Send a POST request to: http://www.foo.com/bar?foz=baz with arguments 'fow' = 'baw' using JSON
    Curl::post('http://www.foo.com/bar', array('foz' => 'baz'), array('fow' => 'baw'), true);

    // Send a POST request to: http://www.foo.com/bar?foz=baz with arguments 'fow' = 'baw' using JSON over SSL
    Curl::post('http://www.foo.com/bar', array('foz' => 'baz'), array('fow' => 'baw'), true, array('SSL_VERIFYPEER' => false));

```

That's all there is to it! Have fun!




## License

This template is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)




## Contact

Jan Oris (developer)

- Email: jan.oris@ixudra.be
- Telephone: +32 496 94 20 57