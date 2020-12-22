<?php

use App\Controllers\User\Base as UserBase;
use Slim\Routing\RouteCollectorProxy;

// User routes
$app->group('/', function(RouteCollectorProxy $user) {
  $user->get('', UserBase::class . ':home');
});
