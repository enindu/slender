<?php

namespace App\Middleware;

use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class UserAuth
{
  public function __construct(private Container $container) {}

  public function __invoke(Request $request, RequestHandlerInterface $handler): Response
  {
    $response = $handler->handle($request);
    $path = $request->getUri()->getPath();
    $sessionExists = isset($_SESSION["auth"]["user"]);
    if(!isset($request->getCookieParams()[$_ENV["app"]["cookie"]["user"]])) {
      if($sessionExists) {
        unset($_SESSION["auth"]["user"]);
      }

      if($path != "/accounts/login" && $path != "/accounts/register") {
        $response = new Response();
        return $response->withHeader("Location", "/accounts/login");
      }

      return $response;
    }

    $eloquent = $this->container->get("eloquent");

    $account = $eloquent->table("users")->where("status", true)->where("unique_id", $request->getCookieParams()[$_ENV["app"]["cookie"]["user"]])->first();
    if($account == null) {
      if($sessionExists) {
        unset($_SESSION["auth"]["user"]);
      }

      setcookie($_ENV["app"]["cookie"]["user"], "expired", strtotime("now") - 1, "/", $_ENV["app"]["domain"], false, true);

      if($path != "/accounts/login" && $path != "/accounts/register") {
        $response = new Response();
        return $response->withHeader("Location", "/accounts/login");
      }

      return $response;
    }

    if($path == "/accounts/login" || $path == "/accounts/register") {
      $response = new Response();
      return $response->withHeader("Location", "/");
    }

    $_SESSION["auth"]["user"] = [
      "id"         => $account->id,
      "role-id"    => $account->role_id,
      "status"     => $account->status,
      "first-name" => $account->first_name,
      "last-name"  => $account->last_name,
      "email"      => $account->email,
      "phone"      => $account->phone
    ];

    return $response;
  }
}
