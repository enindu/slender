<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class PagesController extends BaseController
{
  public function home(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, 'home.twig', [
      'app' => [
        'name'        => $_ENV['APP_NAME'],
        'description' => $_ENV['APP_DESCRIPTION'],
        'keywords'    => $_ENV['APP_KEYWORDS'],
        'author'      => $_ENV['APP_AUTHOR']
      ]
    ]);
  }
}
