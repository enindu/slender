<?php

use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Crypto;

class Cart
{
  public function __invoke(Request $request, RequestHandlerInterface $handler): Response
  {
    $response = $handler->handle($request);
    if(isset($request->getCookieParams()[$_ENV["app"]["cookie"]["cart"]])) {
      return $response;
    }

    setcookie($_ENV["app"]["cookie"]["cart"], Crypto::token(), strtotime("1 day"), "/", $_ENV["app"]["domain"], false, true);
    return $response;
  }
}
