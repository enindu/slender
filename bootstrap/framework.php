<?php

use Slim\Factory\AppFactory;
use System\Framework\ErrorRenderer;

$app = AppFactory::createFromContainer($container);

$routeCacheFile = __DIR__ . "/../cache/routes/cache.php";
$routeCollector = $app->getRouteCollector();
$routeCollector->setCacheFile($routeCacheFile);

require_once __DIR__ . "/../routes/api.php";
require_once __DIR__ . "/../routes/web.php";

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware($_ENV["framework"]["display_error_details"], $_ENV["framework"]["log_errors"], $_ENV["framework"]["log_error_details"]);
$errorHandler = (object) $errorMiddleware->getDefaultErrorHandler();
$errorRenderer = new ErrorRenderer($container);
$errorHandler->registerErrorRenderer("text/html", $errorRenderer);

$app->run();
