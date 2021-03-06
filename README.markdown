[![Build Status](https://travis-ci.org/expectedbehavior/php-docraptor.svg?branch=master)](https://travis-ci.org/expectedbehavior/php-docraptor) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/expectedbehavior/php-docraptor/badges/quality-score.png)](https://scrutinizer-ci.com/g/expectedbehavior/php-docraptor/)

#PHP-DocRaptor

PHP-DocRaptor is a simple API wrapper for [DocRaptor.com](https://docraptor.com/).
You will need a DocRaptor [account](https://docraptor.com/plans) before you can use this library, as it requires a valid API key.

##Dependencies
This wrapper requires PHP 5.4 or newer. PHP 5.4 support will be dropped when it reaches EOL. We strongly advise to migrate your projects to PHP 5.6. Other than that, only the PHP curl extension is needed.

At the moment, this library still works with PHP 5.3, but we don't guarantee that for any future releases.

##Installation

This library is PSR-4 autoloading compliant and you can install it via composer. Just require it in your `composer.json`.
```javascript
"require": {
    "expectedbehavior/php-docraptor": "1.3.0"
}
```

Then run `composer update` resp. `composer install`.

##Usage

```php
$docRaptor = new DocRaptor\ApiWrapper("YOUR_API_KEY_HERE"); // Or omit the API key and pass it in via setter
$docRaptor->setDocumentContent('<h1>Hello!</h1>')->setDocumentType('pdf')->setTest(true)->setName('output.pdf');
$file = $docRaptor->fetchDocument();
```

`fetchDocument` returns document contents by default.  You can optionally provide a file path to the `fetchDocument` method, and the wrapper will attempt to write the returned value to that file path.

###Options

####Setting Prince Specific Options
The API wrapper has the ability to set `prince_options` that is noted in the [API documentation](https://docraptor.com/documentation#pdf_options), where available keys are listed.

Example of setting prince version and [PDF profile](https://en.wikipedia.org/wiki/PDF/A#PDF.2FA-1):

```php
$docRaptor = new DocRaptor\ApiWrapper("YOUR_API_KEY_HERE");
$docRaptor
    ->setDocumentContent('<h1>Hello!</h1>')
    ->setDocumentType('pdf')
    ->setTest(true)
    ->setName('output.pdf')
    ->setDocumentPrinceOptions(array(
        'version' => '10',
        'profile' => 'PDF/A-1b',
    ));
$file = $docRaptor->fetchDocument();
```

####Asynchronous
By default, PHP-DocRaptor submits requests sychronously. However, if you are trying to process large documents, you can  make an asynchronous doc request.  You can choose to do this by passing an argument to the `setAsync` method (`true` for asynchronous, `false` for synchronous request):

```php
$docRaptor->setAsync(true);
```

Synchronous requests will return the file contents where as asynchronous requests will return a json response.  Examples of the response can be found in the [API documentation](https://docraptor.com/documentation#api_async)

####Async Callback URL
If `setAsync(true)` is called and you want DocRaptor to do a POST request to your system when an asynchronous job has finished you can set the callback url via `setCallbackUrl`:

```php
$docRaptor->setCallbackUrl('http://example.com/callback');
```

Details on the POST request can be found in the [API documentation](https://docraptor.com/documentation#api_callback_url)

####HTTPS or HTTP
By default, PHP-DocRaptor submits requests over https.  You can choose to submit via http by passing an argument to the `setSecure` method (`true` for https, `false` for http):

```php
$docRaptor->setSecure(false);
```

NB! It IS not secure, you're basically broadcasting your api key over the network.

### Privacy

By default this library will report usage statistics - the api wrapper version and PHP version - to the DocRaptor service by way of a user agent string. You can disable this by providing a config object to the `ApiWrapper` constructor or the `HttpClient` constructor.

```php
$config     = new DocRaptor\Config(false);
$httpClient = new DocRaptor\HttpClient($config);
$docRaptor  = new DocRaptor\ApiWrapper("YOUR_API_KEY_HERE", $httpClient, $config);
// or
$config    = new DocRaptor\Config(false);
$docRaptor = new DocRaptor\ApiWrapper("YOUR_API_KEY_HERE", null, $config); // will use HttpClient by default
```

### Alternate HTTP Implementation
Since we're injecting a `HttpTransferInterface` interface into the `ApiWrapper` you can either inject the provided `HttpClient` or inject your own implementation of the interface.

```php
$httpClient = new DocRaptor\HttpClient();
$docRaptor  = new DocRaptor\ApiWrapper("YOUR_API_KEY_HERE", $httpClient);
```

The provided `HttpClient` is a very simple domain specific curl wrapper that extracts all curl functions from the `ApiWrapper` which makes it possible to inject a mock client for testing.

## Contributing

If you find a bug, please make a [new GitHub issue](https://github.com/expectedbehavior/php-docraptor/issues/new). If you know how to solve it, make a branch and once you're done make a [new pull request](https://github.com/expectedbehavior/php-docraptor/compare).

When submitting a PR, you will need to [install composer](https://getcomposer.org/doc/00-intro.md) to run the tests. Once you have it installed, in the project root, run `composer install`. To run the tests from the project root, run `vendor/bin/phpunit`. They should all pass! Also we have travis and scrutinizer integration, so you can check those in your PR for things that could be better or don't work.

## Releasing

With the new version number:

* Update [`CHANGELOG.markdown`](https://github.com/expectedbehavior/php-docraptor/blob/master/CHANGELOG.markdown)
* Update [`composer.json`](https://github.com/expectedbehavior/php-docraptor/blob/master/composer.json)
* Update install directions in [`README.markdown`](https://github.com/expectedbehavior/php-docraptor/blob/master/README.markdown#installation)
* Update [`Config.php`](https://github.com/expectedbehavior/php-docraptor/blob/master/src/Config.php)
* `git tag <version>`
* `git push origin master --tags`

After pushing your new tagged version, make sure the new version shows up on [Packagist](https://packagist.org/packages/expectedbehavior/php-docraptor).
