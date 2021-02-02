<?php

use DI\Container;
use Slim\Factory\AppFactory;

require_once __DIR__ . "/vendor/autoload.php";

$ubench = new Ubench();
$ubench->start();

require_once __DIR__ . "/settings/app.php";
require_once __DIR__ . "/settings/system.php";

$container = new Container();

$app = AppFactory::createFromContainer($container);

$routeCollector = $app->getRouteCollector();
$routeCollector->setCacheFile(__DIR__ . "/cache/routes/cache.php");

require_once __DIR__ . "/app/middleware.php";
require_once __DIR__ . "/app/routes.php";

$app->run();

$ubench->end();
file_put_contents(__DIR__ . "/logs/performance.log", "[" . date(DATE_ATOM) . "] " . $_ENV["app"]["name"] . ".DEBUG " . $ubench->getTime() . " | " . $ubench->getMemoryUsage() . "\n");
