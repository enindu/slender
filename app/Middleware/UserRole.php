<?php

namespace App\Middleware;

use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class UserRole
{
  private $container;
  private $roles;

  /**
   * User role constructor
   * 
   * @param Container $container
   * @param int       $roleId
   */
  public function __construct(Container $container, array $roles)
  {
    $this->container = $container;
    $this->roles = $roles;
  }

  /**
   * User role invoker
   * 
   * @param Request                 $request
   * @param RequestHandlerInterface $requestHandler
   * 
   * @throws HttpForbiddenException
   * @return Response
   */
  public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
  {
    // Get database library
    $database = $this->container->get('database');

    // Check role exists
    $roleId = (int) $database->table('user_accounts')->where('unique_id', $request->getCookieParams()[$_ENV['app']['cookie']['user']])->value('role_id');
    $roleExists = array_search($roleId, $this->roles);
    if(!$roleExists) {
      throw new HttpForbiddenException($request);
    }

    // Return response
    return $requestHandler->handle($request);
  }
}
