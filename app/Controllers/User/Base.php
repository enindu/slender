<?php

namespace App\Controllers\User;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;

class Base extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        return $this->viewResponse($response, "@user/home.twig");
    }
}
