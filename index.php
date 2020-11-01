<?php

use DI\Container;
use Slim\Factory\AppFactory;

// Get Composer autoload and bootstrap
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/bootstrap/bootstrap.php";

// Create dependncy container and get library
// containers
$container = new Container();
require_once __DIR__ . "/containers/filesystem.php";
require_once __DIR__ . "/containers/clock.php";
require_once __DIR__ . "/containers/image.php";
require_once __DIR__ . "/containers/database.php";
require_once __DIR__ . "/containers/view.php";
require_once __DIR__ . "/containers/mailer.php";
require_once __DIR__ . "/containers/message.php";
require_once __DIR__ . "/containers/validator.php";

// Create app
$app = AppFactory::createFromContainer($container);

// Set router cache file
$routeCollector = $app->getRouteCollector();
$routeCollector->setCacheFile(__DIR__ . '/cache/routes/cache.php');

// Get middleware and routes
require_once __DIR__ . "/app/middleware.php";
require_once __DIR__ . "/app/routes.php";

// Run app
$app->run();
