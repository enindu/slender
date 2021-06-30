<?php

use App\Controllers\Admin\Accounts;
use App\Controllers\Admin\Admins;
use App\Controllers\Admin\Apis;
use App\Controllers\Admin\Base;
use App\Controllers\Admin\Categories;
use App\Controllers\Admin\Contents;
use App\Controllers\Admin\Files;
use App\Controllers\Admin\Images;
use App\Controllers\Admin\Roles;
use App\Controllers\Admin\Sections;
use App\Controllers\Admin\Subcategories;
use App\Controllers\Admin\Users;
use App\Middleware\Admin\Authentication;
use Slim\Routing\RouteCollectorProxy;

$authentication = new Authentication($container, [
    "/admin/accounts/login",
    "/admin/accounts/register"
]);

$routes = function(RouteCollectorProxy $admin): void {
    $admin->get("", Base::class . ":base");
    $admin->group("/accounts", function(RouteCollectorProxy $accounts) {
        $accounts->map(["GET", "POST"], "/login", Accounts::class . ":login");
        $accounts->map(["GET", "POST"], "/register", Accounts::class . ":register");
        $accounts->map(["GET", "POST"], "/logout", Accounts::class . ":logout");
        $accounts->post("/change-information", Accounts::class . ":changeInformation");
        $accounts->post("/change-password", Accounts::class . ":changePassword");
        $accounts->get("/profile", Accounts::class . ":profile");
    });
    $admin->group("/roles", function(RouteCollectorProxy $roles) {
        $roles->get("", Roles::class . ":base");
        $roles->get("/all", Roles::class . ":all");
        $roles->post("/add", Roles::class . ":add");
        $roles->post("/remove", Roles::class . ":remove");
    });
    $admin->group("/sections", function(RouteCollectorProxy $sections) {
        $sections->get("", Sections::class . ":base");
        $sections->get("/all", Sections::class . ":all");
        $sections->post("/add", Sections::class . ":add");
        $sections->post("/remove", Sections::class . ":remove");
    });
    $admin->group("/apis", function(RouteCollectorProxy $apis) {
        $apis->get("", Apis::class . ":base");
        $apis->get("/all", Apis::class . ":all");
        $apis->post("/add", Apis::class . ":add");
        $apis->post("/activate", Apis::class . ":activate");
        $apis->post("/deactivate", Apis::class . ":deactivate");
        $apis->post("/remove", Apis::class . ":remove");
    });
    $admin->group("/admins", function(RouteCollectorProxy $admins) {
        $admins->get("", Admins::class . ":base");
        $admins->get("/all", Admins::class . ":all");
        $admins->post("/add", Admins::class . ":add");
        $admins->post("/activate", Admins::class . ":activate");
        $admins->post("/deactivate", Admins::class . ":deactivate");
        $admins->post("/remove", Admins::class . ":remove");
    });
    $admin->group("/users", function(RouteCollectorProxy $users) {
        $users->get("", Users::class . ":base");
        $users->get("/all", Users::class . ":all");
        $users->post("/add", Users::class . ":add");
        $users->post("/activate", Users::class . ":activate");
        $users->post("/deactivate", Users::class . ":deactivate");
        $users->post("/remove", Users::class . ":remove");
    });
    $admin->group("/contents", function(RouteCollectorProxy $contents) {
        $contents->get("", Contents::class . ":base");
        $contents->get("/all", Contents::class . ":all");
        $contents->post("/add", Contents::class . ":add");
        $contents->post("/update", Contents::class . ":update");
        $contents->post("/remove", Contents::class . ":remove");
        $contents->get("/{id}", Contents::class . ":single");
    });
    $admin->group("/images", function(RouteCollectorProxy $images) {
        $images->get("", Images::class . ":base");
        $images->get("/all", Images::class . ":all");
        $images->post("/add", Images::class . ":add");
        $images->post("/remove", Images::class . ":remove");
    });
    $admin->group("/files", function(RouteCollectorProxy $files) {
        $files->get("", Files::class . ":base");
        $files->get("/all", Files::class . ":all");
        $files->post("/add", Files::class . ":add");
        $files->post("/remove", Files::class . ":remove");
    });
    $admin->group("/categories", function(RouteCollectorProxy $categories) {
        $categories->get("", Categories::class . ":base");
        $categories->get("/all", Categories::class . ":all");
        $categories->post("/add", Categories::class . ":add");
        $categories->post("/update", Categories::class . ":update");
        $categories->post("/remove", Categories::class . ":remove");
        $categories->get("/{id}", Categories::class . ":single");
    });
    $admin->group("/subcategories", function(RouteCollectorProxy $subcategories) {
        $subcategories->get("", Subcategories::class . ":base");
        $subcategories->get("/all", Subcategories::class . ":all");
        $subcategories->post("/add", Subcategories::class . ":add");
        $subcategories->post("/update", Subcategories::class . ":update");
        $subcategories->post("/remove", Subcategories::class . ":remove");
        $subcategories->get("/{id}", Subcategories::class . ":single");
    });
};

$app->group("/admin", $routes)->add($authentication);
