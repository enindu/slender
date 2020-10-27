<?php

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Base extends Controller
{
  /**
   * Homepage
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @return Response
   */
  public function home(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, 'home.twig');
  }
}
