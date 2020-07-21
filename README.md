# travel-sorter

## About the project

This project proposes a solution for the "Travel Tickets Order" problem
([Eulerian Path Problem](https://en.wikipedia.org/wiki/Eulerian_path)). The problem goes like this:
> You are given a stack of travel tickets that will take you from point A to point B via several stops on the way. 
> All of the tickets are out of order and you don't know where your journey starts, nor where it ends. 
> You must sort the tickets in the right order to complete your journey.

More then only present a solution for the problem, this project also aims to show real examples of object orient 
principles and design patterns on PHP.
- The application is controlled with configurations files (nothing is hard coded), which make it ease to work in 
  different environments (eg: stage, development, production, etc).
- It is 100% covered with unit tests.
- Use [PHP PSR](https://www.php-fig.org/) standards.
- No frameworks were used (besides [PHPUnit]).
- Only four dependencies:
  - [php-di/php-di](https://github.com/PHP-DI/PHP-DI): For dependency injection.
  - [guzzlehttp/psr7](https://github.com/guzzle/psr7): As [PSR-7] implementation,.  
  - [phpunit/phpunit](https://github.com/sebastianbergmann/phpunit): For unit test.  
  - [zendframework/zend-config-aggregator](https://github.com/zendframework/zend-config-aggregator): To merge all of the 
    application configurations together.

## Getting Started

1. Clone the project
1. Start the project with composer:
   ```bash
   $ composer install
   ```
1. Start PHP's built-in web server:
   ```bash
   $ composer run --timeout=0 serve
   ```
1. You can now consume the API on the address http://127.0.0.1:4000

If you want to start the server using port different of 4000, you can start the server manually:
```bash
$ php -S 0.0.0.0:**_YOU_PORT_** -t public/
```

> ##### Linux users
>
> On PHP versions prior to 7.1.14 and 7.2.2, this command might not work as expected due to a bug in PHP that only
> affects linux environments. In such scenarios, you will need to start the
> [built-in web server](http://php.net/manual/en/features.commandline.webserver.php) yourself using the following
> command:
> ```bash
> $ php -S 0.0.0.0:4000 -t public/ public/index.php
> ```

## Modular Application

- This is a modular application, compound of two modules: 
  1. `App`: Where you can find behaviors not related to an API, such as the [sort algorithm], business logic, etc.
  1. `Api`: This is where the API resides. This module only has behaviors related to API requests. There you will
    find the Requests Handlers (AKA controllers), entities used in the API responses, etc.

## Configuration Architecture

The application behavior can be controlled by external configuration files. These "external configuration files"
resides in folder [`config/autoload`]. So far we only have one file there, but any file inside that folder that ends 
with `.global.php` will be merged into one single set of configuration.

### Configurations Available

Each module of the application provides its own configuration through a file called `ConfigProvider`. You can see 
these files at [ConfigProvider of App Module] and [ConfigProvider of Api Module].

Any configuration inside these `ConfigProvider` files can be override by creating the same config entry in a file inside 
the folder [`config/autoload`]. In fact, that is how we control the routes. Take a look in the file [config/autoload/routes.global.php].


## Api Module

The `travel-sorter` provides an API with one endpoint for sorting your tickets: [`POST /api/sort`].

<a id="the-error-response-layout"></a>
### The Error Response
All endpoints, when end in failure, respond with a JSON trying to describe why the error happens (for example, it 
would fail if you forget to send a required parameter). The following is the JSON used to represent an error response. 

```js
{
  // This attribute will always be `true`
  "error": true,
  
  // A string containing more details about the error.
  "detail": "Missing the \"origin\" attribute."
}
 ```

### API endpoints

<a id="post-sort"></a>
#### `POST /api/sort`
Sort a set of tickets. 

**URL params**

None

**Data params**

It expect that the request body contains a JSON with a list of objects describing each ticket, as following:
```js
{
   // List of tickets to sort.
  "tickets": [
    {
      // The kind of the transportation this ticket is related to.
      // Eg: Train, Flight, Bus
      // It is required.
      "transport": "Airport Bus",
      
      // The origin of the trip.
      // For example: a name of a city, airport, etc.
      // It is required.
      "origin": "Barcelona",
    
      // The destiny of the trip.
      // For example: a name of a city, airport, etc.
      // It is required.
      "destiny": "Gerona Airport",
    
      // The seat (if any) where the passenger will sit during the trip.
      // It is optional.
      "seat": "7F",
      
      // The boarding gate, if any.
      // It is optional.
      "gate": "45B",
      
      // Any extra information related to the tick.
      // You can use this to say, for example, where the passengers baggage should be left before boarding.
      // It is optional.
      "extra": "Baggage will be automatically transferred."
    }
    
    // .... MORE ITEMS
  ]
}
```

**Curl example**
```bash
$ curl -X POST 'http://127.0.0.1:4000/api/sort' --data-binary '{
  "tickets": [
    {"transport": "Flight SK455", "origin": "Gerona Airport", "destiny": "Stockholm", "seat": "3A", "extra": "Baggage drop at ticket counter 344."},
    {"transport": "Airport Bus", "origin": "Barcelona", "destiny": "Gerona Airport"},
    {"transport": "Flight SK22", "origin": "Stockholm", "destiny": "New York JFK", "gate": "22B", "seat": "7B", "extra": "Baggage will we automatically transferred from your last leg."},
    {"transport": "Train 78A", "origin": "Madrid", "destiny": "Barcelona", "seat": "45B"}
  ]
}'
```

**Success Response**

- `200 - OK`
It means that your set of tickets were successfully ordered. The response body will be in the same layout you sent in 
the request.

**Error Response**

All error responses will contain a body with [The Error Response Layout].
- `422 - Unprocessable Entity`: When you make a request without a body.
- `422 - Unprocessable Entity`: When you request body does not contain the `tickets` attribute.
- `422 - Unprocessable Entity`: If the `transport` attribute is missing in one of the tickets.
- `422 - Unprocessable Entity`: If the `origin` attribute is missing in one of the tickets.
- `422 - Unprocessable Entity`: If the `destiny` attribute is missing in one of the tickets.
- `405 - Method Not Allowed`: If you do not use the POST method.
- `400 - Bad Request`: If the set of tickets can not be sorted because they have a missing connection between them.
- `400 - Bad Request`: If the journey ends in the same place it starts.
- `500 - Bad Request`: If an unexpected error occurs.

## Composer Scripts

The `travel-sorter` defines three composer scripts, as follow:
- **`serve`**: Start PHP's built-in web server on the port 4000.
- **`test`**: Run all unit tests.
- **`test-coverage`**: Run all unit tests and generates a code coverage report in HTML format in the folder 
  `test-coverage`. Please note that this functionality is only available when the tokenizer and Xdebug extensions are 
  installed.



[PHPUnit]: https://phpunit.de/
[`POST /api/sort`]: #post-sort
[The Error Response Layout]: #the-error-response-layout
[sort algorithm]: https://github.com/stavarengo/travel-sorter/tree/master/src/App/TicketsSorter
[`config/autoload`]: https://github.com/stavarengo/travel-sorter/tree/master/config/autoload
[config/autoload/routes.global.php]: https://github.com/stavarengo/travel-sorter/blob/master/config/autoload/routes.global.php
[ConfigProvider of App Module]: https://github.com/stavarengo/travel-sorter/blob/master/src/App/ConfigProvider.php
[ConfigProvider of Api Module]: https://github.com/stavarengo/travel-sorter/blob/master/src/Api/ConfigProvider.php
[PSR-7]: https://www.php-fig.org/psr/psr-7/
