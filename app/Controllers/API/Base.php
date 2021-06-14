<?php

namespace App\Controllers\API;

use App\Controllers\Controller;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Base extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    $response->getBody()->write(json_encode([
      "status"   => true,
      "code"     => 200,
      "response" => $_ENV["app"]["name"] . " " . $_ENV["app"]["version"] . " - " . $_ENV["app"]["description"]
    ]));
    return $response->withHeader("Content-Type", "application/json");
  }
}
