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
    $cookieExists = isset($request->getCookieParams()[$_ENV["app"]["cookie"]["cart"]]);
    if($cookieExists) {
      return $response;
    }

    setcookie($_ENV["app"]["cookie"]["cart"], Crypto::token(), [
      "expires"  => strtotime("1 day"),
      "path"     => "/",
      "domain"   => $_ENV["app"]["domain"],
      "secure"   => true,
      "httponly" => true,
      "samesite" => "Strict"
    ]);

    return $response;
  }
}
