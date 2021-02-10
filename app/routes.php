<?php

use App\Controllers\Admin\Accounts as AdminAccounts;
use App\Controllers\Admin\Admins as AdminAdmins;
use App\Controllers\Admin\Base as AdminBase;
use App\Controllers\Admin\Images as AdminImages;
use App\Controllers\Admin\Roles as AdminRoles;
use App\Controllers\Admin\Sections as AdminSections;
use App\Controllers\Admin\Users as AdminUsers;
use App\Controllers\User\Base as UserBase;
use App\Middleware\AdminAuth;
use Slim\Routing\RouteCollectorProxy;

$app->group("/admin", function(RouteCollectorProxy $admin) {
  $admin->get("", AdminBase::class . ":base");
  $admin->group("/accounts", function(RouteCollectorProxy $accounts) {
    $accounts->map(["GET", "POST"], "/login", AdminAccounts::class . ":login");
    $accounts->map(["GET", "POST"], "/register", AdminAccounts::class . ":register");
    $accounts->map(["GET", "POST"], "/logout", AdminAccounts::class . ":logout");
    $accounts->get("/profile", AdminAccounts::class . ":profile");
    $accounts->post("/change-information", AdminAccounts::class . ":changeInformation");
    $accounts->post("/change-password", AdminAccounts::class . ":changePassword");
  });
  $admin->group("/roles", function(RouteCollectorProxy $roles) {
    $roles->get("", AdminRoles::class . ":base");
    $roles->get("/all", AdminRoles::class . ":all");
    $roles->post("/add", AdminRoles::class . ":add");
    $roles->post("/remove", AdminRoles::class . ":remove");
  });
  $admin->group("/sections", function(RouteCollectorProxy $sections) {
    $sections->get("", AdminSections::class . ":base");
    $sections->get("/all", AdminSections::class . ":all");
    $sections->post("/add", AdminSections::class . ":add");
    $sections->post("/remove", AdminSections::class . ":remove");
  });
  $admin->group("/admins", function(RouteCollectorProxy $admins) {
    $admins->get("", AdminAdmins::class . ":base");
    $admins->get("/all", AdminAdmins::class . ":all");
    $admins->post("/add", AdminAdmins::class . ":add");
    $admins->post("/activate", AdminAdmins::class . ":activate");
    $admins->post("/deactivate", AdminAdmins::class . ":deactivate");
    $admins->post("/remove", AdminAdmins::class . ":remove");
  });
  $admin->group("/users", function(RouteCollectorProxy $users) {
    $users->get("", AdminUsers::class . ":base");
    $users->get("/all", AdminUsers::class . ":all");
    $users->post("/add", AdminUsers::class . ":add");
    $users->post("/activate", AdminUsers::class . ":activate");
    $users->post("/deactivate", AdminUsers::class . ":deactivate");
    $users->post("/remove", AdminUsers::class . ":remove");
  });
  $admin->group("/images", function(RouteCollectorProxy $images) {
    $images->get("", AdminImages::class . ":base");
    $images->get("/all", AdminImages::class . ":all");
    $images->post("/add", AdminImages::class . ":add");
    $images->post("/remove", AdminImages::class . ":remove");
  });
})->add(new AdminAuth($container));

$app->group("", function(RouteCollectorProxy $user) {
  $user->get("/", UserBase::class . ":base");
});
