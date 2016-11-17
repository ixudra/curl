ixudra/curl
================

Custom PHP cURL library for the Laravel 4 or 5 framework - developed by [Ixudra](http://ixudra.be).




## Installation

Pull this package in through Composer.

```js

    {
        "require": {
            "ixudra/curl": "6.*"
        }
    }

```


### Laravel 5.* Integration

Add the service provider to your `config/app.php` file:

```php

    'providers'     => array(

        //...
        Ixudra\Curl\CurlServiceProvider::class,

    ),

```

Add the facade to your `config/app.php` file:

```php

    'facades'       => array(

        //...
        'Curl'          => Ixudra\Curl\Facades\Curl::class,

    ),

```


### Laravel 4.* Integration

Add the service provider to your `app/config/app.php` file:

```php

    'providers'     => array(

        //...
        'Ixudra\Curl\CurlServiceProvider',

    ),

```

Add the facade to your `app/config/app.php` file:

```php

    'facades'       => array(

        //...
        'Curl'          => 'Ixudra\Curl\Facades\Curl',

    ),

```


### Lumen 5.* integration

In your `bootstrap/app.php`, make sure you've un-commented the following line (around line 26):

```
$app->withFacades();
```

Then, register your class alias:
```
class_alias('Ixudra\Curl\Facades\Curl', 'Curl');
```

Finally, you have to register your ServiceProvider (around line 70-80):

```
/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// $app->register('App\Providers\AppServiceProvider');

// Package service providers
$app->register('Ixudra\Curl\Providers\CurlServiceProvider');
```


### Integration without Laravel

Create a new instance of the `CurlService` where you would like to use the package:

```php

    $curlService = new \Ixudra\Curl\CurlService();

```




## Usage

### Laravel usage

The package provides an easy interface for sending cURL requests from your application. The package provides a fluent
interface similar the Laravel query builder to easily configure the request. There are several utility methods that allow
you to easily add certain options to the request. If no utility method applies, you can also use the general `withOption`
method.

### Sending GET requests

In order to send a `GET` request, you need to use the `get()` method that is provided by the package:

```php

    // Send a GET request to: http://www.foo.com/bar
    $response = Curl::to('http://www.foo.com/bar')
        ->get();

    // Send a GET request to: http://www.foo.com/bar?foz=baz
    $response = Curl::to('http://www.foo.com/bar')
        ->withData( array( 'foz' => 'baz' ) )
        ->get();

    // Send a GET request to: http://www.foo.com/bar?foz=baz using JSON
    $response = Curl::to('http://www.foo.com/bar')
        ->withData( array( 'foz' => 'baz' ) )
        ->asJson()
        ->get();

```


### Sending POST requests

Post requests work similar to `GET` requests, but use the `post()` method instead:

```php

    // Send a POST request to: http://www.foo.com/bar
    $response = Curl::to('http://www.foo.com/bar')
        ->post();

    // Send a POST request to: http://www.foo.com/bar
    $response = Curl::to('http://www.foo.com/bar')
        ->withData( array( 'foz' => 'baz' ) )
        ->post();

    // Send a POST request to: http://www.foo.com/bar with arguments 'foz' = 'baz' using JSON
    $response = Curl::to('http://www.foo.com/bar')
        ->withData( array( 'foz' => 'baz' ) )
        ->asJson()
        ->post();

    // Send a POST request to: http://www.foo.com/bar with arguments 'foz' = 'baz' using JSON and return as associative array
    $response = Curl::to('http://www.foo.com/bar')
        ->withData( array( 'foz' => 'baz' ) )
        ->asJson( true )
        ->post();

```


### Sending PUT requests

Put requests work similar to `POST` requests, but use the `put()` method instead:

```php

    // Send a PUT request to: http://www.foo.com/bar/1 with arguments 'foz' = 'baz' using JSON
    $response = Curl::to('http://www.foo.com/bar/1')
        ->withData( array( 'foz' => 'baz' ) )
        ->asJson()
        ->put();

```


### Sending PATCH requests

Patch requests work similar to `POST` requests, but use the `patch()` method instead:

```php

    // Send a PATCH request to: http://www.foo.com/bar/1 with arguments 'foz' = 'baz' using JSON
    $response = Curl::to('http://www.foo.com/bar/1')
        ->withData( array( 'foz' => 'baz' ) )
        ->asJson()
        ->patch();

```


### Sending DELETE requests

Delete requests work similar to `GET` requests, but use the `delete()` method instead:

```php

    // Send a DELETE request to: http://www.foo.com/bar/1 using JSON
    $response = Curl::to('http://www.foo.com/bar/1')
        ->asJson()
        ->delete();

```


### Sending files via Curl

For sending files via a POST request, you can use the `containsFile` method to correctly format a request before sending:

```php

    $response = Curl::to('http://foo.com/bar.png')
        ->withContentType('multipart/form-data')
        ->withData( array( 'foz' => 'baz' ) )
        ->containsFile()
        ->post();

```


### Downloading files

For downloading a file, you can use the `download()` method:

```php

    // Download an image from: file http://www.foo.com/bar.png
    $response = Curl::to('http://foo.com/bar.png')
        ->withContentType('image/png')
        ->download('/path/to/dir/image.png');

```


### Debugging requests

In case a request fails, it might be useful to get debug the request. In this case, you can use the `enableDebug()` method.
This method uses one parameter, which is the name of the file in which the debug information is to be stored:

```php

    // Send a GET request to http://www.foo.com/bar and log debug information in /path/to/dir/logFile.txt
    $response = Curl::to('http://www.foo.com/bar')
            ->enableDebug('/path/to/dir/logFile.txt');
            ->get();

```


### Using response objects

Be default, the package will only return the content of the request. In some cases, it might also be useful to know
additional request information, such as the HTTP status code and error messages should they occur. In this case, you
can use the `returnResponseObject()` method, which will return an stdClass that contains additional information as 
well as the response content:

```php

    // Send a GET request to http://www.foo.com/bar and return a response object with additional information
    $response = Curl::to('http://www.foo.com/bar')
            ->returnResponseObject();
            ->get();
            
    $content = $response->content;

```

The response object will look like this:

```
{
   "content": 'Message content here',
   "status": 200,
   "error": 'Error message goes here'       // Only added if an error occurs
}
```


### Using cURL options

You can add various cURL options to the request using of several utility methods such as `withHeader()` for adding a
header to the request, or use the general `withOption()` method if no utility method applies. The package will
automatically prepend the options with the `CURLOPT_` prefix. It is worth noting that the package does not perform
any validation on the cURL options. Additional information about available cURL options can be found
[here](http://php.net/manual/en/function.curl-setopt.php).

| Method                |  Default value    | Description                                                       |
|-----------------------|-------------------|-------------------------------------------------------------------|
| withTimeout()         |  30 seconds       | Set the timeout of the request                                    |
| allowRedirect()       |  false            | Allow the request to be redirected internally                     |
| asJsonRequest()       |  false            | Submit the request data as JSON                                   |
| asJsonResponse()      |  false            | Decode the response data from JSON                                |
| asJson()              |  false            | Utility method to set both `asJsonRequest()` and `asJsonResponse()` at the same time   |
| withHeader()          |  array()          | Add an HTTP header to the request                                 |
| withHeaders()         |  array()          | Add multiple HTTP headers to the request                          |
| withContentType()     |  none             | Set the content type of the response                              |
| containsFile()        |  false            | Should be used to submit files through forms                      |
| withData()            |  array()          | Add an array of data to sent with the request (GET or POST)       |
| withOption()          |  none             | Generic method to add any cURL option to the request              |

For specific information regarding parameters and return times, I encourage you to take a look at 
`ixudra\curl\src\Ixudra\Curl\Builder.php` which has extensive doc blocks that contain all the necessary information
for each specific method.



### Usage without Laravel

Usage without Laravel is identical to usage described previously. The only difference is that you will not be able to
use the facades to access the `CurlService`.

```php

    $curlService = new \Ixudra\Curl\CurlService();

    // Send a GET request to: http://www.foo.com/bar
    $response = $curlService->to('http://www.foo.com/bar')
        ->get();

    // Send a POST request to: http://www.foo.com/bar
    $response = $curlService->to('http://www.foo.com/bar')
        ->post();

    // Send a PUT request to: http://www.foo.com/bar
    $response = $curlService->to('http://www.foo.com/bar')
        ->put();

    // Send a DELETE request to: http://www.foo.com/bar
    $response = $curlService->to('http://www.foo.com/bar')
        ->delete();

```




## Planning

 - Add additional utility methods for other cURL options
 - Add contract to allow different HTTP providers such as Guzzle




## License

This template is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)




## Contact

Jan Oris (developer)

- Email: jan.oris@ixudra.be
- Telephone: +32 496 94 20 57
