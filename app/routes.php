<?php

use App\Controllers\Admin\Base as AdminBase;
use App\Controllers\User\Base as UserBase;
use Slim\Routing\RouteCollectorProxy;
use System\Slender\Middleware\AdminAuth;

$app->group("/admin", function(RouteCollectorProxy $admin) {
  $admin->get("", AdminBase::class . ':base');
})->add(new AdminAuth($container));

$app->group("", function(RouteCollectorProxy $user) {
  $user->get("/", UserBase::class . ":base");
});
