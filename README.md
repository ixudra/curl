Ixudra/Curl
=============

Custom PHP curl library for the Laravel 4 framework - developed by Ixudra.

This package can be used by anyone at any given time, but keep in mind that it is optimized for my personal custom workflow. It may not suit your project perfectly and modifications may be in order.




## Installation

Pull this package in through Composer.

```js

    {
        "require": {
            "ixudra/curl": "0.1.*"
        }
    }

```

Add the service provider to your `config/app.php` file:

```php

    providers       => array(

        //...
        'Ixudra\Curl\CurlServiceProvider',

    )

```

Add the facade to your `config/app.php` file:

```php

    facades         => array(

        //...
        'Curl'          => 'Ixudra\Curl\Facades\Curl',

    )

```




## Usage

### GET requests

The package provides an easy interface for sending CURL requests from your application. Optionally, you can also include several `GET` parameters that will automatically be added to the base URL by the package automatically. Lastly, the package also has a parameter that allows you to easily mark a request as a json requests. The package will automatically handle the conversion from and to json to PHP wherever needed. The default value of this parameter is `false`:

```php

    Curl::get('http://www.foo.com/bar');

    Curl::get('http://www.foo.com/bar', array('foz' => 'baz'));

    Curl::get('http://www.foo.com/bar', array('foz' => 'baz'), true);

```

The package will subsequently following URL: `http://www.foo.com/bar?foz=baz`.


### POST requests

The package also allows you to send `POST` requests for your application. The first and second parameter are identical to the `Curl::get()` method. The `POST` parameters can be past on as the third parameter. The fourth and last parameter can be used to mark the request as a json requests. The package will automatically handle the conversion from and to json to PHP wherever needed.

```php

    Curl::post('http://www.foo.com/bar', array(), array('fow' => 'baw'));

    Curl::post('http://www.foo.com/bar', array('foz' => 'baz'), array('fow' => 'baw'));

    Curl::post('http://www.foo.com/bar', array('foz' => 'baz'), array('fow' => 'baw'), true);

```

That's all there is to it! Have fun!