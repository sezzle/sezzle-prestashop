<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb9bcbd94097e02a1b8ce760410dfd6c2
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Sezzle\\' => 7,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Http\\Client\\' => 16,
            'PrestaShop\\Module\\Sezzle\\' => 25,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Sezzle\\' => 
        array (
            0 => __DIR__ . '/..' . '/sezzle/php-sdk/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Http\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-client/src',
        ),
        'PrestaShop\\Module\\Sezzle\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
    );

    public static $classMap = array (
        'GuzzleHttp\\BodySummarizer' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/BodySummarizer.php',
        'GuzzleHttp\\BodySummarizerInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/BodySummarizerInterface.php',
        'GuzzleHttp\\Client' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Client.php',
        'GuzzleHttp\\ClientInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/ClientInterface.php',
        'GuzzleHttp\\ClientTrait' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/ClientTrait.php',
        'GuzzleHttp\\Cookie\\CookieJar' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/CookieJar.php',
        'GuzzleHttp\\Cookie\\CookieJarInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/CookieJarInterface.php',
        'GuzzleHttp\\Cookie\\FileCookieJar' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/FileCookieJar.php',
        'GuzzleHttp\\Cookie\\SessionCookieJar' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/SessionCookieJar.php',
        'GuzzleHttp\\Cookie\\SetCookie' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/SetCookie.php',
        'GuzzleHttp\\Exception\\BadResponseException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/BadResponseException.php',
        'GuzzleHttp\\Exception\\ClientException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/ClientException.php',
        'GuzzleHttp\\Exception\\ConnectException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/ConnectException.php',
        'GuzzleHttp\\Exception\\GuzzleException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/GuzzleException.php',
        'GuzzleHttp\\Exception\\InvalidArgumentException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/InvalidArgumentException.php',
        'GuzzleHttp\\Exception\\RequestException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/RequestException.php',
        'GuzzleHttp\\Exception\\ServerException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/ServerException.php',
        'GuzzleHttp\\Exception\\TooManyRedirectsException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/TooManyRedirectsException.php',
        'GuzzleHttp\\Exception\\TransferException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/TransferException.php',
        'GuzzleHttp\\HandlerStack' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/HandlerStack.php',
        'GuzzleHttp\\Handler\\CurlFactory' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/CurlFactory.php',
        'GuzzleHttp\\Handler\\CurlFactoryInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/CurlFactoryInterface.php',
        'GuzzleHttp\\Handler\\CurlHandler' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/CurlHandler.php',
        'GuzzleHttp\\Handler\\CurlMultiHandler' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/CurlMultiHandler.php',
        'GuzzleHttp\\Handler\\EasyHandle' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/EasyHandle.php',
        'GuzzleHttp\\Handler\\MockHandler' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/MockHandler.php',
        'GuzzleHttp\\Handler\\Proxy' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/Proxy.php',
        'GuzzleHttp\\Handler\\StreamHandler' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/StreamHandler.php',
        'GuzzleHttp\\MessageFormatter' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/MessageFormatter.php',
        'GuzzleHttp\\MessageFormatterInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/MessageFormatterInterface.php',
        'GuzzleHttp\\Middleware' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Middleware.php',
        'GuzzleHttp\\Pool' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Pool.php',
        'GuzzleHttp\\PrepareBodyMiddleware' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/PrepareBodyMiddleware.php',
        'GuzzleHttp\\Promise\\AggregateException' => __DIR__ . '/..' . '/guzzlehttp/promises/src/AggregateException.php',
        'GuzzleHttp\\Promise\\CancellationException' => __DIR__ . '/..' . '/guzzlehttp/promises/src/CancellationException.php',
        'GuzzleHttp\\Promise\\Coroutine' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Coroutine.php',
        'GuzzleHttp\\Promise\\Create' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Create.php',
        'GuzzleHttp\\Promise\\Each' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Each.php',
        'GuzzleHttp\\Promise\\EachPromise' => __DIR__ . '/..' . '/guzzlehttp/promises/src/EachPromise.php',
        'GuzzleHttp\\Promise\\FulfilledPromise' => __DIR__ . '/..' . '/guzzlehttp/promises/src/FulfilledPromise.php',
        'GuzzleHttp\\Promise\\Is' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Is.php',
        'GuzzleHttp\\Promise\\Promise' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Promise.php',
        'GuzzleHttp\\Promise\\PromiseInterface' => __DIR__ . '/..' . '/guzzlehttp/promises/src/PromiseInterface.php',
        'GuzzleHttp\\Promise\\PromisorInterface' => __DIR__ . '/..' . '/guzzlehttp/promises/src/PromisorInterface.php',
        'GuzzleHttp\\Promise\\RejectedPromise' => __DIR__ . '/..' . '/guzzlehttp/promises/src/RejectedPromise.php',
        'GuzzleHttp\\Promise\\RejectionException' => __DIR__ . '/..' . '/guzzlehttp/promises/src/RejectionException.php',
        'GuzzleHttp\\Promise\\TaskQueue' => __DIR__ . '/..' . '/guzzlehttp/promises/src/TaskQueue.php',
        'GuzzleHttp\\Promise\\TaskQueueInterface' => __DIR__ . '/..' . '/guzzlehttp/promises/src/TaskQueueInterface.php',
        'GuzzleHttp\\Promise\\Utils' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Utils.php',
        'GuzzleHttp\\Psr7\\AppendStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/AppendStream.php',
        'GuzzleHttp\\Psr7\\BufferStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/BufferStream.php',
        'GuzzleHttp\\Psr7\\CachingStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/CachingStream.php',
        'GuzzleHttp\\Psr7\\DroppingStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/DroppingStream.php',
        'GuzzleHttp\\Psr7\\FnStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/FnStream.php',
        'GuzzleHttp\\Psr7\\Header' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Header.php',
        'GuzzleHttp\\Psr7\\InflateStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/InflateStream.php',
        'GuzzleHttp\\Psr7\\LazyOpenStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/LazyOpenStream.php',
        'GuzzleHttp\\Psr7\\LimitStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/LimitStream.php',
        'GuzzleHttp\\Psr7\\Message' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Message.php',
        'GuzzleHttp\\Psr7\\MessageTrait' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/MessageTrait.php',
        'GuzzleHttp\\Psr7\\MimeType' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/MimeType.php',
        'GuzzleHttp\\Psr7\\MultipartStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/MultipartStream.php',
        'GuzzleHttp\\Psr7\\NoSeekStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/NoSeekStream.php',
        'GuzzleHttp\\Psr7\\PumpStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/PumpStream.php',
        'GuzzleHttp\\Psr7\\Query' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Query.php',
        'GuzzleHttp\\Psr7\\Request' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Request.php',
        'GuzzleHttp\\Psr7\\Response' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Response.php',
        'GuzzleHttp\\Psr7\\Rfc7230' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Rfc7230.php',
        'GuzzleHttp\\Psr7\\ServerRequest' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/ServerRequest.php',
        'GuzzleHttp\\Psr7\\Stream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Stream.php',
        'GuzzleHttp\\Psr7\\StreamDecoratorTrait' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/StreamDecoratorTrait.php',
        'GuzzleHttp\\Psr7\\StreamWrapper' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/StreamWrapper.php',
        'GuzzleHttp\\Psr7\\UploadedFile' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/UploadedFile.php',
        'GuzzleHttp\\Psr7\\Uri' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Uri.php',
        'GuzzleHttp\\Psr7\\UriNormalizer' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/UriNormalizer.php',
        'GuzzleHttp\\Psr7\\UriResolver' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/UriResolver.php',
        'GuzzleHttp\\Psr7\\Utils' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Utils.php',
        'GuzzleHttp\\RedirectMiddleware' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/RedirectMiddleware.php',
        'GuzzleHttp\\RequestOptions' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/RequestOptions.php',
        'GuzzleHttp\\RetryMiddleware' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/RetryMiddleware.php',
        'GuzzleHttp\\TransferStats' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/TransferStats.php',
        'GuzzleHttp\\Utils' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Utils.php',
        'Payment' => __DIR__ . '/../..' . '/classes/Payment.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\GatewayRegion' => __DIR__ . '/../..' . '/src/Handler/GatewayRegion.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Order' => __DIR__ . '/../..' . '/src/Handler/Order.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Payment\\Authorization' => __DIR__ . '/../..' . '/src/Handler/Payment/Authorization.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Payment\\Capture' => __DIR__ . '/../..' . '/src/Handler/Payment/Capture.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Payment\\Refund' => __DIR__ . '/../..' . '/src/Handler/Payment/Refund.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Payment\\Release' => __DIR__ . '/../..' . '/src/Handler/Payment/Release.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Service\\Authentication' => __DIR__ . '/../..' . '/src/Handler/Service/Authentication.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Service\\Capture' => __DIR__ . '/../..' . '/src/Handler/Service/Capture.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Service\\Order' => __DIR__ . '/../..' . '/src/Handler/Service/Order.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Service\\Refund' => __DIR__ . '/../..' . '/src/Handler/Service/Refund.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Service\\Release' => __DIR__ . '/../..' . '/src/Handler/Service/Release.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Service\\Session' => __DIR__ . '/../..' . '/src/Handler/Service/Session.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Service\\Tokenization' => __DIR__ . '/../..' . '/src/Handler/Service/Tokenization.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Service\\Util' => __DIR__ . '/../..' . '/src/Handler/Service/Util.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Session' => __DIR__ . '/../..' . '/src/Handler/Session.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Tokenization' => __DIR__ . '/../..' . '/src/Handler/Tokenization.php',
        'PrestaShop\\Module\\Sezzle\\Handler\\Util' => __DIR__ . '/../..' . '/src/Handler/Util.php',
        'PrestaShop\\Module\\Sezzle\\Setup\\Installer' => __DIR__ . '/../..' . '/src/Setup/Installer.php',
        'PrestaShop\\Module\\Sezzle\\Setup\\InstallerFactory' => __DIR__ . '/../..' . '/src/Setup/InstallerFactory.php',
        'Psr\\Http\\Client\\ClientExceptionInterface' => __DIR__ . '/..' . '/psr/http-client/src/ClientExceptionInterface.php',
        'Psr\\Http\\Client\\ClientInterface' => __DIR__ . '/..' . '/psr/http-client/src/ClientInterface.php',
        'Psr\\Http\\Client\\NetworkExceptionInterface' => __DIR__ . '/..' . '/psr/http-client/src/NetworkExceptionInterface.php',
        'Psr\\Http\\Client\\RequestExceptionInterface' => __DIR__ . '/..' . '/psr/http-client/src/RequestExceptionInterface.php',
        'Psr\\Http\\Message\\MessageInterface' => __DIR__ . '/..' . '/psr/http-message/src/MessageInterface.php',
        'Psr\\Http\\Message\\RequestInterface' => __DIR__ . '/..' . '/psr/http-message/src/RequestInterface.php',
        'Psr\\Http\\Message\\ResponseInterface' => __DIR__ . '/..' . '/psr/http-message/src/ResponseInterface.php',
        'Psr\\Http\\Message\\ServerRequestInterface' => __DIR__ . '/..' . '/psr/http-message/src/ServerRequestInterface.php',
        'Psr\\Http\\Message\\StreamInterface' => __DIR__ . '/..' . '/psr/http-message/src/StreamInterface.php',
        'Psr\\Http\\Message\\UploadedFileInterface' => __DIR__ . '/..' . '/psr/http-message/src/UploadedFileInterface.php',
        'Psr\\Http\\Message\\UriInterface' => __DIR__ . '/..' . '/psr/http-message/src/UriInterface.php',
        'Sezzle' => __DIR__ . '/../..' . '/sezzle.php',
        'SezzleAbstractModuleFrontController' => __DIR__ . '/../..' . '/controllers/front/abstract.php',
        'SezzleTokenization' => __DIR__ . '/../..' . '/classes/SezzleTokenization.php',
        'SezzleTransaction' => __DIR__ . '/../..' . '/classes/SezzleTransaction.php',
        'Sezzle\\Config' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Config.php',
        'Sezzle\\Factory\\AuthFactory' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Factory/AuthFactory.php',
        'Sezzle\\Factory\\SessionFactory' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Factory/SessionFactory.php',
        'Sezzle\\HttpClient\\ClientService' => __DIR__ . '/..' . '/sezzle/php-sdk/src/HttpClient/ClientService.php',
        'Sezzle\\HttpClient\\GuzzleFactory' => __DIR__ . '/..' . '/sezzle/php-sdk/src/HttpClient/GuzzleFactory.php',
        'Sezzle\\HttpClient\\GuzzleHttpClient' => __DIR__ . '/..' . '/sezzle/php-sdk/src/HttpClient/GuzzleHttpClient.php',
        'Sezzle\\HttpClient\\HttpClientInterface' => __DIR__ . '/..' . '/sezzle/php-sdk/src/HttpClient/HttpClientInterface.php',
        'Sezzle\\HttpClient\\RequestException' => __DIR__ . '/..' . '/sezzle/php-sdk/src/HttpClient/RequestException.php',
        'Sezzle\\HttpClient\\Response' => __DIR__ . '/..' . '/sezzle/php-sdk/src/HttpClient/Response.php',
        'Sezzle\\Model\\AuthCredentials' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/AuthCredentials.php',
        'Sezzle\\Model\\CustomerOrder' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/CustomerOrder.php',
        'Sezzle\\Model\\ErrorResponse' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/ErrorResponse.php',
        'Sezzle\\Model\\Order' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Order.php',
        'Sezzle\\Model\\Order\\Authorization' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Order/Authorization.php',
        'Sezzle\\Model\\Order\\Authorization\\State' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Order/Authorization/State.php',
        'Sezzle\\Model\\Order\\Capture' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Order/Capture.php',
        'Sezzle\\Model\\Order\\Links' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Order/Links.php',
        'Sezzle\\Model\\Order\\Refund' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Order/Refund.php',
        'Sezzle\\Model\\Order\\Release' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Order/Release.php',
        'Sezzle\\Model\\Session' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Session.php',
        'Sezzle\\Model\\Session\\Customer' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Session/Customer.php',
        'Sezzle\\Model\\Session\\Customer\\Address' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Session/Customer/Address.php',
        'Sezzle\\Model\\Session\\Order' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Session/Order.php',
        'Sezzle\\Model\\Session\\Order\\Amount' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Session/Order/Amount.php',
        'Sezzle\\Model\\Session\\Order\\Discount' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Session/Order/Discount.php',
        'Sezzle\\Model\\Session\\Order\\Item' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Session/Order/Item.php',
        'Sezzle\\Model\\Session\\Tokenize' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Session/Tokenize.php',
        'Sezzle\\Model\\Session\\Url' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Session/Url.php',
        'Sezzle\\Model\\Token' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Token.php',
        'Sezzle\\Model\\Tokenize' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Tokenize.php',
        'Sezzle\\Model\\Tokenize\\Customer' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Model/Tokenize/Customer.php',
        'Sezzle\\Services\\AuthenticationService' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Services/AuthenticationService.php',
        'Sezzle\\Services\\CaptureService' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Services/CaptureService.php',
        'Sezzle\\Services\\OrderService' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Services/OrderService.php',
        'Sezzle\\Services\\RefundService' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Services/RefundService.php',
        'Sezzle\\Services\\ReleaseService' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Services/ReleaseService.php',
        'Sezzle\\Services\\SessionService' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Services/SessionService.php',
        'Sezzle\\Services\\TokenizationService' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Services/TokenizationService.php',
        'Sezzle\\Util' => __DIR__ . '/..' . '/sezzle/php-sdk/src/Util.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb9bcbd94097e02a1b8ce760410dfd6c2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb9bcbd94097e02a1b8ce760410dfd6c2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb9bcbd94097e02a1b8ce760410dfd6c2::$classMap;

        }, null, ClassLoader::class);
    }
}