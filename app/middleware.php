<?php

use System\Slender\Middleware\Session;
use System\Slim\ErrorRenderer;

$app->add(new Session());

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer("text/html", new ErrorRenderer($container));
