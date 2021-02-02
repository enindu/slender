<?php

namespace App\Controllers\User;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Base
{
  public function base(Request $request, Response $response, array $data): Response
  {
    $response->getBody()->write($_ENV["app"]["name"] . " " . $_ENV["app"]["version"]);
    return $response->withHeader("Content-Type", "text/html");
  }
}
