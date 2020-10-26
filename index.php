<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Symfony\Component\Dotenv\Dotenv;

// Composer autoload
require_once __DIR__ . "/vendor/autoload.php";

// Environment
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.config');

// System configurations
date_default_timezone_set($_ENV['APP_TIMEZONE']);
mb_internal_encoding($_ENV['APP_CHARSET']);

// Containers
$container = new Container();
require_once __DIR__ . "/containers/session.php";
require_once __DIR__ . "/containers/filesystem.php";
require_once __DIR__ . "/containers/clock.php";
require_once __DIR__ . "/containers/image.php";
require_once __DIR__ . "/containers/database.php";
require_once __DIR__ . "/containers/view.php";
require_once __DIR__ . "/containers/email.php";
require_once __DIR__ . "/containers/validator.php";
require_once __DIR__ . "/containers/error.php";

// App
$app = AppFactory::createFromContainer($container);

// Middleware
require_once __DIR__ . "/app/middleware.php";

// Body parsing middleware
$app->addBodyParsingMiddleware();

// Routing middleware
$app->addRoutingMiddleware();

// Error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$defaultErrorHandler = (object) $errorMiddleware->getDefaultErrorHandler();
$defaultErrorHandler->registerErrorRenderer('text/html', $container->get('renderer'));

// Routes
require_once __DIR__ . "/app/routes.php";

// Run
$app->run();
