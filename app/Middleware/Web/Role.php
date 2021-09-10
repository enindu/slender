<?php

namespace App\Middleware\Web;

use App\Models\Account;
use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\App\Middleware;

class Role extends Middleware
{
    public function __construct(public Container $container, private array $roles)
    {
        parent::__construct($container);
    }

    public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        $cookies = $request->getCookieParams();
        $validationError = $this->validateData($cookies, [
            $_ENV["settings"]["cookie"]["name"]["account"] => "required|alpha_num|min:67|max:67"
        ]);
        if($validationError != null) {
            throw new HttpForbiddenException($request);
        }

        $account = Account::where("status", true)->where("unique_id", $cookies[$_ENV["settings"]["cookie"]["name"]["user"]])->first();
        $roleExists = array_search($account->role->title, $this->roles);
        if($roleExists === false) {
            throw new HttpForbiddenException($request);
        }

        return $requestHandler->handle($request);
    }
}
