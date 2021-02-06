<?php

namespace App\Controllers\User;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controllers\Controller;

class Base extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@user/home.twig");
  }
}
