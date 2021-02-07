<?php

use App\Controllers\Admin\Accounts as AdminAccounts;
use App\Controllers\Admin\Base as AdminBase;
use App\Controllers\User\Base as UserBase;
use App\Middleware\AdminAuth;
use Slim\Routing\RouteCollectorProxy;

$app->group("/admin", function(RouteCollectorProxy $admin) {
  $admin->get("", AdminBase::class . ":base");
  $admin->group("/accounts", function(RouteCollectorProxy $accounts) {
    $accounts->map(["GET", "POST"], "/login", AdminAccounts::class . ":login");
    $accounts->map(["GET", "POST"], "/register", AdminAccounts::class . ":register");
    $accounts->map(["GET", "POST"], "/logout", AdminAccounts::class . ":logout");
  });
})->add(new AdminAuth($container));

$app->group("", function(RouteCollectorProxy $user) {
  $user->get("/", UserBase::class . ":base");
});
