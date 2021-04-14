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

ini_set('internal_encoding', 'utf-8');
define('TIMEZONE', 'America/Havana');
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set(TIMEZONE);

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool)$_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
