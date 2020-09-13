<?php

use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

require_once __DIR__ . "/vendor/autoload.php";

$app = AppFactory::create();

$app->get('/', function(Request $request, Response $response, array $data): Response {
  $response->getBody()->write('Slender 0.1.0-dev');

  return $response;
});

$app->run();
