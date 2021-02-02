<?php

namespace App\Controllers;

use Slim\Psr7\Response;

class Controller
{
  protected function view(Response $response, string $template, array $data = []): Response
  {
    $twig = require_once __DIR__ . "/../../libraries/twig.php";
    $response->getBody()->write($twig->render($template, $data));
    return $response->withHeader("Content-Type", "text/html");
  }
}
