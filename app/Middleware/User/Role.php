<?php

namespace App\Middleware\Admin;

use App\Models\User;
use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Middleware;

class Role extends Middleware
{
    public function __construct(private Container $container, private array $roles)
    {
        parent::__construct($container);
    }

    public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        $cookies = $request->getCookieParams();
        $user = User::where("status", true)->where("unique_id", $cookies[$_ENV["app"]["cookie"]["user"]])->first();

        $roleExists = array_search($user->role->title, $this->roles);
        if($roleExists === false) {
            throw new HttpForbiddenException($request);
        }

        return $requestHandler->handle($request);
    }
}
