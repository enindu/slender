<?php

use App\Errors\Renderer;

// Session middleware
$app->add($container->get('session-middleware'));

// Body parsing middleware
$app->addBodyParsingMiddleware();

// Routing middleware
$app->addRoutingMiddleware();

// Error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('text/html', new Renderer($container));
