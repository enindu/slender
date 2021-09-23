<?php

namespace App\Middleware\Web\User;

use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\App\Middleware;

class ShoppingCart extends Middleware
{
    public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        $response = $requestHandler->handle($request);

        $cookies = $request->getCookieParams();
        $validationError = $this->validateData($cookies, [
            $_ENV["settings"]["cookie"]["name"]["shopping_cart"] => "required|alpha_num|min:67|max:67"
        ]);
        if($validationError != null) {
            return $response;
        }

        $shoppingCartId = $this->createToken();
        setcookie($_ENV["settings"]["cookie"]["name"]["shopping_cart"], $shoppingCartId, [
            "expires"  => 0,
            "path"     => "/",
            "domain"   => $_ENV["settings"]["domain"],
            "secure"   => $_ENV["settings"]["cookie"]["secure"],
            "httponly" => $_ENV["settings"]["cookie"]["http_only"],
            "samesite" => $_ENV["settings"]["cookie"]["same_site"]
        ]);

        return $response;
    }
}
