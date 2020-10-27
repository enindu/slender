<?php

use App\Middleware\Session;

$container->set('session-middleware', function(): Session {
  return new Session([
    'lifetime'  => $_ENV['middleware']['session']['lifetime'],
    'path'      => $_ENV['middleware']['session']['path'],
    'domain'    => $_ENV['middleware']['session']['domain'],
    'secure'    => $_ENV['middleware']['session']['secure'],
    'http-only' => $_ENV['middleware']['session']['http-only'],
    'name'      => $_ENV['middleware']['session']['name']
  ]);
});
