<?php

use App\Errors\Renderer;
use App\Middleware\HtmlMinifier;
use App\Middleware\Session;

// Minifier middleware
$app->add(new HtmlMinifier($container));

// Session middleware
$app->add(new Session());

// Body parsing middleware
$app->addBodyParsingMiddleware();

// Routing middleware
$app->addRoutingMiddleware();

// Error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('text/html', new Renderer($container));
