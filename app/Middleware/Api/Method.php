<?php

namespace App\Middleware\Api;

use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\App\Middleware;

class Method extends Middleware
{
    public function __construct(public Container $container, private string $method)
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

        return $requestHandler->handle($request);
    }
}
