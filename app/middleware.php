<?php

use App\Errors\Renderer;
use App\Middleware\Session;

// Session middleware
$app->add(new Session([
  'lifetime'  => $_ENV['middleware']['session']['lifetime'],
  'path'      => $_ENV['middleware']['session']['path'],
  'domain'    => $_ENV['middleware']['session']['domain'],
  'secure'    => $_ENV['middleware']['session']['secure'],
  'http-only' => $_ENV['middleware']['session']['http-only'],
  'name'      => $_ENV['middleware']['session']['name']
]));

// Body parsing middleware
$app->addBodyParsingMiddleware();

// Routing middleware
$app->addRoutingMiddleware();

// Error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('text/html', new Renderer($container));
