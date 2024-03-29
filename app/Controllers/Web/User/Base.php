<?php

namespace App\Controllers\Web\User;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\App\Controller;

class Base extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        return $this->viewResponse($response, "web/user/home.twig");
    }
}
