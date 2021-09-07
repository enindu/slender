<?php

namespace App\Middleware\Api;

use App\Models\Api;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\App\Middleware;

class Authentication extends Middleware
{
    public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        $accept = $request->getHeaderLine("Accept");
        if($accept != "application/json") {
            return $this->newJsonResponse([
                "status"  => false,
                "message" => "Not acceptable."
            ], 406);
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
            "token"    => "required|alpha_num|min:67|max:67"
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
