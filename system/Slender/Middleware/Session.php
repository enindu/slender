<?php

namespace System\Slender\Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Session
{
  public function __invoke(Request $request, RequestHandlerInterface $handler): Response
  {
    $response = $handler->handle($request);
    $cookieExists = isset($request->getCookieParams()[$_ENV["middleware"]["session"]["name"]]);
    if($cookieExists) {
      return $response;
    }

    session_set_cookie_params([
      "lifetime" => $_ENV["middleware"]["session"]["lifetime"],
      "path"     => $_ENV["middleware"]["session"]["path"],
      "domain"   => $_ENV["middleware"]["session"]["domain"],
      "secure"   => $_ENV["middleware"]["session"]["secure"],
      "httponly" => $_ENV["middleware"]["session"]["http-only"]
    ]);
    session_name($_ENV["middleware"]["session"]["name"]);
    session_id(md5(uniqid(bin2hex(random_bytes(32)))));
    session_start();
    return $response;
  }
}
