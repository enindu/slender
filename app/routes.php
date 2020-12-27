<?php

use App\Controllers\Admin\Accounts as AdminAccounts;
use App\Controllers\Admin\Admins as AdminAdmins;
use App\Controllers\Admin\Base as AdminBase;
use App\Controllers\User\Base as UserBase;
use App\Middleware\AdminAuth;
use App\Middleware\AdminRole;
use Slim\Routing\RouteCollectorProxy;

// Admin routes
$app->group('/admin', function(RouteCollectorProxy $admin) use($container) {
  $admin->get('', AdminBase::class . ':home');
  $admin->group('/accounts', function(RouteCollectorProxy $accounts) {
    $accounts->map(['GET', 'POST'], '/login', AdminAccounts::class . ':login');
    $accounts->map(['GET', 'POST'], '/register', AdminAccounts::class . ':register');
    $accounts->map(['GET', 'POST'], '/logout', AdminAccounts::class . ':logout');
    $accounts->get('/profile', AdminAccounts::class . ':profile');
    $accounts->post('/change-information', AdminAccounts::class . ':changeInformation');
    $accounts->post('/change-password', AdminAccounts::class . ':changePassword');
  });
  $admin->group('/admins', function(RouteCollectorProxy $admins) {
    $admins->get('', AdminBase::class . ':admins');
    $admins->post('/add', AdminAdmins::class . ':add');
    $admins->post('/activate', AdminAdmins::class . ':activate');
    $admins->post('/deactivate', AdminAdmins::class . ':deactivate');
    $admins->post('/remove', AdminAdmins::class . ':remove');
  })->add(new AdminRole($container, [1]));
})->add(new AdminAuth($container));

// User routes
$app->group('/', function(RouteCollectorProxy $user) {
  $user->get('', UserBase::class . ':home');
});
