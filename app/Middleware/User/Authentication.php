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
    private Response $response;
    private string $path;
    private int|false $privatePathExists;
    private int|false $publicPathExists;

    public function __construct(private Container $container, private array $privatePaths, private array $publicPaths = [])
    {
        parent::__construct($container);
    }

    public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        $this->response = $requestHandler->handle($request);

        $this->path = $request->getUri()->getPath();
        $this->privatePathExists = array_search($this->path, $this->privatePaths);
        $this->publicPathExists = array_search($this->path, $this->publicPaths);

        $cookies = $request->getCookieParams();
        $cookieExists = isset($cookies[$_ENV["app"]["cookie"]["user"]]);
        if(!$cookieExists) {
            return $this->loggedOutResponse(true, false, false);
        }

        $uniqueId = $cookies[$_ENV["app"]["cookie"]["user"]];
        $user = User::where("status", true)->where("unique_id", $uniqueId)->first();
        if($user == null) {
            return $this->loggedOutResponse(true, true, true);
        }

        $sessionId = session_id();
        if($sessionId != $user->session_id) {
            return $this->loggedOutResponse(true, true, true);
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

        return $this->loggedInResponse();
    }

    private function loggedOutResponse(bool $clearSession, bool $clearCookie, bool $redirect): Response
    {
        if($clearSession) {
            unset($_SESSION["user"]);
        }

        if($clearCookie) {
            setcookie($_ENV["app"]["cookie"]["user"], "expired", [
                "expires"  => strtotime("yesterday"),
                "path"     => "/",
                "domain"   => $_ENV["app"]["domain"],
                "secure"   => true,
                "httponly" => true,
                "samesite" => "Strict"
            ]);
        }

        if($this->privatePathExists === false && $this->publicPathExists === false) {
            return $this->newRedirectResponse("/accounts/login");
        }

        if($redirect) {
            return $this->newRedirectResponse($this->path);
        }

        return $this->response;
    }

    private function loggedInResponse(): Response
    {
        if($this->privatePathExists !== false && $this->publicPathExists === false) {
            return $this->newRedirectResponse("/");
        }

        return $this->response;
    }
}
