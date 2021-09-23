<?php

namespace App\Middleware\Web\Admin;

use App\Models\Account;
use App\Models\Admin;
use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\App\Middleware;

class Authentication extends Middleware
{
    private Response $response;
    private string $path;
    private int|bool $privatePathExists;
    private int|bool $publicPathExists;

    public function __construct(protected Container $container, private array $privatePaths, private array $publicPaths = [])
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
        $validationError = $this->validateData($cookies, [
            $_ENV["settings"]["cookie"]["name"]["admin"] => "required|alpha_num|min:67|max:67"
        ]);
        if($validationError != null) {
            return $this->loggedOutResponse(true, false, false);
        }

        $uniqueId = $cookies[$_ENV["settings"]["cookie"]["name"]["admin"]];
        $admin = Admin::where("status", true)->where("unique_id", $uniqueId)->first();
        if($admin == null) {
            return $this->loggedOutResponse(true, true, true);
        }

        $sessionId = session_id();
        if($sessionId != $admin->session_id) {
            return $this->loggedOutResponse(true, true, false);
        }

        $_SESSION["admin"] = [
            "id"   => $admin->id,
            "role" => $admin->role->title
        ];

        return $this->loggedInResponse();
    }

    private function loggedOutResponse(bool $clearSession, bool $clearCookie, bool $redirect): Response
    {
        if($clearSession) {
            unset($_SESSION["admin"]);
        }

        if($clearCookie) {
            setcookie($_ENV["settings"]["cookie"]["name"]["admin"], "expired", [
                "expires"  => strtotime("yesterday"),
                "path"     => "/",
                "domain"   => $_ENV["settings"]["domain"],
                "secure"   => $_ENV["settings"]["cookie"]["secure"],
                "httponly" => $_ENV["settings"]["cookie"]["http_only"],
                "samesite" => $_ENV["settings"]["cookie"]["same_site"]
            ]);
        }

        if($redirect) {
            return $this->newRedirectResponse($this->path);
        }

        if($this->privatePathExists === false && $this->publicPathExists === false) {
            return $this->newRedirectResponse("/admin/accounts/login");
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
