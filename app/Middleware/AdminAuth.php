<?php

namespace App\Middleware;

use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class AdminAuth
{
  public function __construct(private Container $container) {}

  public function __invoke(Request $request, RequestHandlerInterface $handler): Response
  {
    $response = $handler->handle($request);
    $path = $request->getUri()->getPath();

    $sessionExists = isset($_SESSION["auth"]["admin"]);
    $cookieExists = isset($request->getCookieParams()[$_ENV["app"]["cookie"]["admin"]]);
    if(!$cookieExists) {
      if($sessionExists) {
        unset($_SESSION["auth"]["admin"]);
      }

      if($path != "/admin/accounts/login" && $path != "/admin/accounts/register") {
        $response = new Response();
        return $response->withHeader("Location", "/admin/accounts/login");
      }

      return $response;
    }

    $eloquent = $this->container->get("eloquent");

    $account = $eloquent->table("admins")->where("status", true)->where("unique_id", $request->getCookieParams()[$_ENV["app"]["cookie"]["admin"]])->first();
    if($account == null) {
      if($sessionExists) {
        unset($_SESSION["auth"]["admin"]);
      }

      setcookie($_ENV["app"]["cookie"]["admin"], "expired", [
        "expires"  => strtotime("now") - 1,
        "path"     => "/admin",
        "domain"   => $_ENV["app"]["domain"],
        "secure"   => true,
        "httponly" => true,
        "samesite" => "Strict"
      ]);

      if($path != "/admin/accounts/login" && $path != "/admin/accounts/register") {
        $response = new Response();
        return $response->withHeader("Location", "/admin/accounts/login");
      }

      return $response;
    }

    $sessionID = session_id();
    if($sessionID != $account->session_id) {
      if($sessionExists) {
        unset($_SESSION["auth"]["admin"]);
      }

      setcookie($_ENV["app"]["cookie"]["admin"], "expired", [
        "expires"  => strtotime("now") - 1,
        "path"     => "/admin",
        "domain"   => $_ENV["app"]["domain"],
        "secure"   => true,
        "httponly" => true,
        "samesite" => "Strict"
      ]);

      if($path != "/admin/accounts/login" && $path != "/admin/accounts/register") {
        $response = new Response();
        return $response->withHeader("Location", "/admin/accounts/login");
      }

      return $response;
    }

    if($path == "/admin/accounts/login" || $path == "/admin/accounts/register") {
      $response = new Response();
      return $response->withHeader("Location", "/admin");
    }

    $_SESSION["auth"]["admin"] = [
      "id"       => $account->id,
      "role-id"  => $account->role_id,
      "status"   => $account->status,
      "username" => $account->username
    ];

    return $response;
  }
}
