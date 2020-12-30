<?php

namespace App\Middleware;

use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class UserAuth
{
  private $container;

  /**
   * User auth constructor
   * 
   * @param Container $container
   */
  public function __construct(Container $container)
  {
    $this->container = $container;
  }

  /**
   * User auth invoker
   * 
   * @param Request                 $request
   * @param RequestHandlerInterface $requestHandler
   * 
   * @return Response
   */
  public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
  {
    // Check cookie exists
    $requestPath = $request->getUri()->getPath();
    $sessionExists = isset($_SESSION['auth']['user']);
    $cookieExists = isset($request->getCookieParams()[$_ENV['app']['cookie']['user']]);
    if(!$cookieExists) {
      // Check session exists
      if($sessionExists) {
        unset($_SESSION['auth']['user']);
      }

      // Check request path
      if($requestPath != '/accounts/login' && $requestPath != '/accounts/register') {
        $response = new Response();
        return $response->withHeader('location', '/accounts/login');
      }

      // Return response
      return $requestHandler->handle($request);
    }

    // Get database library
    $database = $this->container->get('database');

    // Check account
    $account = $database->table('users')->where('status', true)->where('unique_id', $request->getCookieParams()[$_ENV['app']['cookie']['user']])->first();
    if($account == null) {
      // Check session exists
      if($sessionExists) {
        unset($_SESSION['auth']['user']);
      }

      // Remove cookie
      setcookie($_ENV['app']['cookie']['user'], 'expired', strtotime('now') - 1, '/');

      // Check request path
      if($requestPath != '/accounts/login' && $requestPath != '/accounts/register') {
        $response = new Response();
        return $response->withHeader('location', '/accounts/login');
      }

      // Return response
      return $requestHandler->handle($request);
    }

    // Check request path
    if($requestPath == '/accounts/login' || $requestPath == '/accounts/register') {
      $response = new Response();
      return $response->withHeader('location', '/');
    }

    // Get session
    $_SESSION['auth']['user'] = [
      'id'         => (int) $account->id,
      'role-id'    => (int) $account->role_id,
      'status'     => (bool) $account->status,
      'first-name' => $account->first_name,
      'last-name'  => $account->last_name,
      'email'      => $account->email,
      'phone'      => $account->phone,
    ];

    // Return response
    return $requestHandler->handle($request);
  }
}
