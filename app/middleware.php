<?php

use App\Errors\Renderer;

// App middleware
$app->add($container->get('session-middleware'));

// Slim middleware
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler    = $errorMiddleware->getDefaultErrorHandler();

$errorHandler->registerErrorRenderer('text/html', new Renderer($container));
