<?php

namespace App\Middleware\Api;

use App\Models\Api;
use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Middleware;

class Authentication extends Middleware
{
    public function __construct(private Container $container, private string $method = "PUT")
    {
        parent::__construct($container);
    }

    public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        $method = $request->getMethod();
        if($method != $this->method) {
            return $this->newJsonResponse([
                "status"  => false,
                "message" => "Method not allowed."
            ], 405);
        }

        $contentType = $request->getHeaderLine("Content-Type");
        if($contentType != "application/json") {
            return $this->newJsonResponse([
                "status"  => false,
                "message" => "Not acceptable."
            ], 406);
        }

        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "username" => "required|alpha_num|max:6",
            "token"    => "required"
        ]);
        if($validationError != null) {
            return $this->newJsonResponse([
                "status"  => false,
                "message" => "Forbidden."
            ], 403);
        }

        $username = $inputs["username"];
        $token = $inputs["token"];

        $api = Api::where("status", true)->where("username", $username)->where("token", $token)->first();
        if($api == null) {
            return $this->newJsonResponse([
                "status"  => false,
                "message" => "Forbidden."
            ], 403);
        }

        return $requestHandler->handle($request);
    }
}
