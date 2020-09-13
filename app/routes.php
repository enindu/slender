<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

$app->get('/', function(Request $request, Response $response, array $data): Response {
  $response->getBody()->write('Slender 0.1.0-dev');

  return $response;
});
