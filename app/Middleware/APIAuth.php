<?php

namespace App\Middleware;

use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class APIAuth
{
  public function __construct(
    private Container $container,
    private string $method = "PUT",
    private string $contentType = "application/json"
  ) {}

  public function __invoke(Request $request, RequestHandlerInterface $handler): Response
  {
    $exitingMethod = strtolower($this->method);
    $requestedMethod = strtolower($request->getMethod());
    if($exitingMethod != $requestedMethod) {
      $response = new Response();
      $response->getBody()->write(json_encode([
        "status"   => false,
        "code"     => 405,
        "response" => "Method not allowed."
      ]));
      return $response->withStatus(405)->withHeader("Content-Type", $this->contentType);
    }

    $exitingContentType = strtolower($this->contentType);
    $requestedContentType = strtolower($request->getHeaderLine("Content-Type"));
    if($exitingContentType != $requestedContentType) {
      $response = new Response();
      $response->getBody()->write(json_encode([
        "status"   => false,
        "code"     => 406,
        "response" => "Not acceptable."
      ]));
      return $response->withStatus(406)->withHeader("Content-Type", $this->contentType);
    }

    $inputs = $request->getParsedBody();
    $usernameExists = isset($inputs["username"]);
    $tokenExists = isset($inputs["token"]);
    if(!$usernameExists && !$tokenExists) {
      $response = new Response();
      $response->getBody()->write(json_encode([
        "status"   => false,
        "code"     => 403,
        "response" => "Forbidden."
      ]));
      return $response->withStatus(403)->withHeader("Content-Type", $this->contentType);
    }

    $username = $inputs["username"];
    $token = $inputs["token"];

    $eloquent = $this->container->get("eloquent");

    $api = $eloquent->table("apis")->where("username", $username)->where("token", $token)->first();
    if($api == null) {
      $response = new Response();
      $response->getBody()->write(json_encode([
        "status"   => false,
        "code"     => 403,
        "response" => "Forbidden."
      ]));
      return $response->withStatus(403)->withHeader("Content-Type", $this->contentType);
    }

    return $handler->handle($request);
  }
}
