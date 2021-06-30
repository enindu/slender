<?php

namespace App\Middleware\User;

use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Middleware;

class Cart extends Middleware
{
    public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        $response = $requestHandler->handle($request);

        $cookies = $request->getCookieParams();
        $cookieExists = isset($cookies[$_ENV["app"]["cookie"]["cart"]]);
        if($cookieExists) {
            return $response;
        }

        $cartId = $this->createToken();
        setcookie($_ENV["app"]["cookie"]["cart"], $cartId, [
            "expires"  => strtotime("tomorrow"),
            "path"     => "/",
            "domain"   => $_ENV["app"]["domain"],
            "secure"   => true,
            "httponly" => true,
            "samesite" => "Strict"
        ]);

        return $response;
    }
}
