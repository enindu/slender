<?php

namespace App\Middleware\Admin;

use App\Models\Admin;
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
        $cookieExists = isset($cookies[$_ENV["app"]["cookie"]["admin"]]);
        if(!$cookieExists) {
            unset($_SESSION["admin"]);

            if($pathExists === false) {
                return $this->resetRequest("/admin/accounts/login");
            }

            return $response;
        }

        $uniqueId = $cookies[$_ENV["app"]["cookie"]["admin"]];
        $admin = Admin::where("status", true)->where("unique_id", $uniqueId)->first();
        if($admin == null) {
            return $this->resetRequest("/admin/accounts/login");
        }

        $sessionId = session_id();
        if($sessionId != $admin->session_id) {
            return $this->resetRequest("/admin/accounts/login");
        }

        if($pathExists !== false) {
            return $this->newRedirectResponse("/admin");
        }

        $_SESSION["admin"] = [
            "id"       => $admin->id,
            "status"   => $admin->status,
            "username" => $admin->username,
            "role"     => $admin->role->title
        ];

        return $response;
    }

    private function resetRequest(string $path): Response
    {
        unset($_SESSION["admin"]);
        setcookie($_ENV["app"]["cookie"]["admin"], "expired", [
            "expires"  => strtotime("yesterday"),
            "path"     => "/admin",
            "domain"   => $_ENV["app"]["domain"],
            "secure"   => true,
            "httponly" => true,
            "samesite" => "Strict"
        ]);

        return $this->newRedirectResponse($path);
    }
}
