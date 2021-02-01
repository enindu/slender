<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

$app->get("/", function(Request $request, Response $response, array $data): Response {
  $response->getBody()->write("Hello world!");
  return $response;
});
