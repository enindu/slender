<?php

use Slim\Factory\AppFactory;

require_once __DIR__ . "/configurations/bootstrap.php";

$app = AppFactory::create();

$routeCollenctor = $app->getRouteCollector();
$routeCollenctor->setCacheFile(__DIR__ . "/cache/routes/cache.php");

require_once __DIR__ . "/app/routes.php";

$app->run();
