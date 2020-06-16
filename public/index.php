<?php
/*
if($_SERVER['SERVER_NAME'] === 'infonet.sensesofcuba.com')
{
    if($_SERVER['HTTPS'] === 'off')
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: https://infonet.sensesofcuba.com/index.php");
        exit();
    }

    if($_SERVER['REQUEST_URI'] !== '/index.php/' && strpos($_SERVER['REQUEST_URI'], '/index.php/') === 0)
    {
        $request_uri = substr($_SERVER['REQUEST_URI'], 10);

        if(file_exists('./'.$request_uri) && !is_dir('./'.$request_uri))
        {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://infonet.sensesofcuba.com/".$request_uri);
            exit();
        }

    }
}
*/

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/config/bootstrap.php';


if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
