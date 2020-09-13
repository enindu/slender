<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class PagesController extends BaseController
{
  public function home(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, 'home.twig');
  }
}
