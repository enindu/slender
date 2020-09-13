<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Pages extends Base
{
  public function home(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, 'home.twig');
  }
}
