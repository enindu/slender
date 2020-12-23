<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\AdminAccount;
use App\Models\AdminRole;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Accounts extends Controller
{
  /**
   * Register page and function
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @return Response
   */
  public function register(Request $request, Response $response, array $data): Response
  {
    // Check request method
    $requestMethod = $request->getMethod();
    if($requestMethod == 'GET') {
      return $this->view($response, '@admin/accounts.register.twig', [
        'roles' => AdminRole::get()
      ]);
    }
    if($requestMethod == 'POST') {
      // Check validation
      $inputs = $request->getParsedBody();
      $validation = $this->validate($inputs, [
        'username'         => 'required|max:6',
        'role-id'          => 'required|integer',
        'password'         => 'required|min:6|max:32',
        'confirm-password' => 'required|same:password'
      ]);
      if($validation != null) {
        throw new HttpBadRequestException($request, reset($validation));
      }

      // Get inputs
      $username = trim($inputs['username']);
      $roleId = (int) trim($inputs['role-id']);
      $password = trim($inputs['password']) . $_ENV['app']['key'];

      // Check role
      $role = AdminRole::where('id', $roleId)->first();
      if($role == null) {
        throw new HttpBadRequestException($request, 'There is no role found');
      }

      // Get clock library
      $clock = $this->container->get('clock');

      // Update database
      AdminAccount::insert([
        'role_id'    => $roleId,
        'unique_id'  => md5(uniqid(bin2hex(random_bytes(32)))),
        'username'   => $username,
        'password'   => password_hash($password, PASSWORD_BCRYPT),
        'created_at' => $clock::now(),
        'updated_at' => $clock::now()
      ]);

      // Return response
      return $response->withHeader('location', '/admin/accounts/login');
    }
  }
}
