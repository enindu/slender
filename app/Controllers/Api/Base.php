<?php

namespace App\Controllers\Api;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;

class Base extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        return $this->jsonResponse($response, [
            "status"  => true,
            "message" => $_ENV["app"]["name"] . " " . $_ENV["app"]["version"] . " - " . $_ENV["app"]["description"]
        ]);
    }
}
