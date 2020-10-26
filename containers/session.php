<?php

use App\Middleware\Session;

$container->set('session', function(): Session {
  return new Session([
    'lifetime'  => $_ENV['SESSION_LIFETIME'],
    'path'      => $_ENV['SESSION_PATH'],
    'domain'    => $_ENV['SESSION_DOMAIN'],
    'secure'    => $_ENV['SESSION_SECURE'],
    'http-only' => $_ENV['SESSION_HTTP_ONLY'],
    'name'      => $_ENV['SESSION_NAME']
  ]);
});
