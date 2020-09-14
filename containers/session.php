<?php

use Slim\Middleware\Session;

$container->set('session', function(): Session {
  return new Session([
    'lifetime'    => intval($_ENV['SESSION_LIFETIME']),
    'path'        => $_ENV['SESSION_PATH'],
    'domain'      => $_ENV['SESSION_DOMAIN'],
    'secure'      => $_ENV['SESSION_SECURE'] === "true" ? true : false,
    'httpOnly'    => $_ENV['SESSION_HTTP_ONLY'] === "true" ? true : false,
    'name'        => $_ENV['SESSION_NAME'],
    'autorefresh' => $_ENV['SESSION_AUTO_REFRESH'] === "true" ? true : false,
    'handler'     => null
  ]);
});
