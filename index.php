<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . "/vendor/autoload.php";

$dotenv = (object) new Dotenv();

$dotenv->load(__DIR__ . '/.config');

date_default_timezone_set($_ENV['APP_TIMEZONE']);
mb_internal_encoding($_ENV['APP_CHARSET']);

$container = (object) new Container();

require_once __DIR__ . "/containers/session.php";
require_once __DIR__ . "/containers/filesystem.php";
require_once __DIR__ . "/containers/clock.php";
require_once __DIR__ . "/containers/image.php";
require_once __DIR__ . "/containers/database.php";
require_once __DIR__ . "/containers/view.php";
require_once __DIR__ . "/containers/email.php";
require_once __DIR__ . "/containers/validator.php";
require_once __DIR__ . "/containers/error.php";

$app = (object) AppFactory::createFromContainer($container);

require_once __DIR__ . "/app/middleware.php";

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$displayErrorDetails = $_ENV['ERROR_DISPLAY_ERROR_DETAILS'] === "true" ? true : false;
$logErrors = $_ENV['ERROR_LOG_ERRORS'] === "true" ? true : false;
$logErrorDetails = $_ENV['ERROR_LOG_ERROR_DETAILS'] === "true" ? true : false;

$errorMiddleware = (object) $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);
$defaultErrorHandler = (object) $errorMiddleware->getDefaultErrorHandler();

$defaultErrorHandler->registerErrorRenderer('text/html', $container->get('renderer'));

require_once __DIR__ . "/app/routes.php";

$app->run();
