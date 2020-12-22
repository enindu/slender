<?php

namespace App\Middleware;

use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class AdminAuth
{
  private $container;

  /**
   * Admin auth constructor
   * 
   * @param Container $container
   */
  public function __construct(Container $container)
  {
    $this->container = $container;
  }

  /**
   * Admin auth invoker
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
    $sessionExists = isset($_SESSION['auth']['admin']);
    $cookieExists = isset($request->getCookieParams()[$_ENV['app']['cookie']['admin']]);
    if(!$cookieExists) {
      // Check session exists
      if($sessionExists) {
        unset($_SESSION['auth']['admin']);
      }

      // Check request path
      if($requestPath != '/admin/accounts/login') {
        $response = new Response();
        return $response->withHeader('location', '/admin/accounts/login');
      }

      // Return response
      return $requestHandler->handle($request);
    }

    // Get database library
    $database = $this->container->get('database');

    // Check account
    $account = $database->table('admin_accounts')->where('unique_id', $request->getCookieParams()[$_ENV['app']['cookie']['admin']])->first();
    if($account == null) {
      // Check session exists
      if($sessionExists) {
        unset($_SESSION['auth']['admin']);
      }

      // Remove cookie
      setcookie($_ENV['app']['cookie']['admin'], 'expired', strtotime('now') - 1, '/');

      // Check request path
      if($requestPath != '/admin/accounts/login') {
        $response = new Response();
        return $response->withHeader('location', '/admin/accounts/login');
      }

      // Return response
      return $requestHandler->handle($request);
    }

    // Check request path
    if($requestPath == '/admin/accounts/login') {
      $response = new Response();
      return $response->withHeader('location', '/admin');
    }

    // Get session
    $_SESSION['auth']['admin'] = [
      'id'       => $account->id,
      'role-id'  => $account->role_id,
      'username' => $account->username
    ];

    // Return response
    return $requestHandler->handle($request);
  }
}
