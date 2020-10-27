<?php

namespace App\Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Session
{
  private $settings;

  /**
   * Session constructor
   * 
   * @param array $settings
   */
  public function __construct(array $settings)
  {
    $this->settings = [
      'name'      => isset($settings['name']) ? $settings['name'] : 'slender',
      'lifetime'  => isset($settings['lifetime']) ? $settings['lifetime'] : 0,
      'path'      => isset($settings['path']) ? $settings['path'] : '/',
      'domain'    => isset($settings['domain']) ? $settings['domain'] : '',
      'secure'    => isset($settings['secure']) ? $settings['secure'] : false,
      'http-only' => isset($settings['http-only']) ? $settings['http-only'] : false
    ];
  }

  /**
   * Session invoker
   * 
   * @param Request                 $request
   * @param RequestHandlerInterface $requestHandler
   * 
   * @return Response
   */
  public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
  {
    // Check session cookie exists
    $sessionCookieExists = isset($request->getCookieParams()[$this->settings['name']]);
    if($sessionCookieExists) {
      return $requestHandler->handle($request);
    }

    // Set session cookies parameters, name, ID
    // and start session
    session_set_cookie_params([
      'lifetime' => $this->settings['lifetime'],
      'path'     => $this->settings['path'],
      'domain'   => $this->settings['domain'],
      'secure'   => $this->settings['secure'],
      'httponly' => $this->settings['http-only']
    ]);

    session_name($this->settings['name']);
    session_id(md5(uniqid(bin2hex(random_bytes(32)))));
    session_start();

    // Return response
    return $requestHandler->handle($request);
  }
}
