<?php

use App\Controllers\Admin\Base as AdminBase;
use App\Controllers\User\Base as UserBase;
use App\Middleware\AdminAuth;
use Slim\Routing\RouteCollectorProxy;

$app->group('/admin', function(RouteCollectorProxy $admin) {
  $admin->get('', AdminBase::class . ':home');
})->add(new AdminAuth($container));

// User routes
$app->group('/', function(RouteCollectorProxy $user) {
  $user->get('', UserBase::class . ':home');
});
