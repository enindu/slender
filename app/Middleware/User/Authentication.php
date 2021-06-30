<?php

namespace App\Middleware\User;

use App\Models\User;
use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Middleware;

class Authentication extends Middleware
{
    public function __construct(private Container $container, private array $paths)
    {
        parent::__construct($container);
    }

    public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        $response = $requestHandler->handle($request);

        $path = $request->getUri()->getPath();
        $pathExists = array_search($path, $this->paths);

        $cookies = $request->getCookieParams();
        $cookieExists = isset($cookies[$_ENV["app"]["cookie"]["user"]]);
        if(!$cookieExists) {
            unset($_SESSION["user"]);
            if($pathExists === false) {
                return $this->resetRequest("/accounts/login");
            }

            return $response;
        }

        $uniqueId = $cookies[$_ENV["app"]["cookie"]["user"]];
        $user = User::where("status", true)->where("unique_id", $uniqueId)->first();
        if($user == null) {
            return $this->resetRequest("/accounts/login");
        }

        $sessionId = session_id();
        if($sessionId != $user->session_id) {
            return $this->resetRequest("/accounts/login");
        }

        if($pathExists !== false) {
            return $this->newRedirectResponse("/");
        }

        $_SESSION["user"] = [
            "id"         => $user->id,
            "status"     => $user->status,
            "first-name" => $user->first_name,
            "last-name"  => $user->last_name,
            "email"      => $user->email,
            "phone"      => $user->phone,
            "role"       => $user->role->title
        ];

        return $response;
    }

    private function resetRequest(string $path): Response
    {
        unset($_SESSION["user"]);

        $expires = strtotime("yesterday");
        setcookie($_ENV["app"]["cookie"]["user"], "expired", [
            "expires"  => $expires,
            "path"     => "/",
            "domain"   => $_ENV["app"]["domain"],
            "secure"   => true,
            "httponly" => true,
            "samesite" => "Strict"
        ]);

        return $this->newRedirectResponse($path);
    }
}
