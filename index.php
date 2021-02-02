<?php

use Slim\Factory\AppFactory;

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/settings/app.php";
require_once __DIR__ . "/settings/system.php";

$app = AppFactory::create();

$routeCollector = $app->getRouteCollector();
$routeCollector->setCacheFile(__DIR__ . "/cache/routes/cache.php");

require_once __DIR__ . "/app/middleware.php";
require_once __DIR__ . "/app/routes.php";

$app->run();
