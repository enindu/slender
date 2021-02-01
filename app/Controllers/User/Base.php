<?php

namespace App\Controllers\User;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Base
{
  /**
   * Base page
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @return Response
   */
  public function base(Request $request, Response $response, array $data): Response
  {
    $response->getBody()->write("Hello world!");
    return $response;
  }
}
