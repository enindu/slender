<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\AdminAccount;
use App\Models\Role;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Accounts extends Controller
{
  /**
   * Login page and function
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @throws HttpBadRequestException
   * @return Response
   */
  public function login(Request $request, Response $response, array $data): Response
  {
    // Check request method
    $requestMethod = $request->getMethod();
    if($requestMethod == 'GET') {
      return $this->view($response, '@admin/accounts.login.twig');
    }
    if($requestMethod == 'POST') {
      // Check validation
      $inputs = $request->getParsedBody();
      $validation = $this->validate($inputs, [
        'username' => 'required|max:6',
        'password' => 'required|min:6|max:32'
      ]);
      if($validation != null) {
        throw new HttpBadRequestException($request, reset($validation) . '.');
      }

      // Get inputs
      $username = trim($inputs['username']);
      $password = trim($inputs['password']) . $_ENV['app']['key'];

      // Check account
      $account = AdminAccount::where('status', true)->where('username', $username)->first();
      if($account == null) {
        throw new HttpBadRequestException($request, 'There is no account found.');
      }

      // Check password matches
      $passwordMatches = password_verify($password, $account->password);
      if(!$passwordMatches) {
        throw new HttpBadRequestException($request, 'Password is invalid.');
      }

      // Set cookie
      setcookie($_ENV['app']['cookie']['admin'], $account->unique_id, strtotime('1 week'), '/');

      // Return response
      return $response->withHeader('location', '/admin');
    }
  }

  /**
   * Register page and function
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @throws HttpBadRequestException
   * @return Response
   */
  public function register(Request $request, Response $response, array $data): Response
  {
    // Check request method
    $requestMethod = $request->getMethod();
    if($requestMethod == 'GET') {
      return $this->view($response, '@admin/accounts.register.twig', [
        'roles' => Role::get()
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
        throw new HttpBadRequestException($request, reset($validation) . '.');
      }

      // Get inputs
      $username = trim($inputs['username']);
      $roleId = (int) trim($inputs['role-id']);
      $password = trim($inputs['password']) . $_ENV['app']['key'];

      // Check role
      $role = Role::where('id', $roleId)->first();
      if($role == null) {
        throw new HttpBadRequestException($request, 'There is no role found.');
      }

      // Check account
      $account = AdminAccount::where('username', $username)->first();
      if($account != null) {
        throw new HttpBadRequestException($request, 'There is an account already using that username.');
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

  /**
   * Logout page and function
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @return Response
   */
  public function logout(Request $request, Response $response, array $data): Response
  {
    // Check request method
    $requestMethod = $request->getMethod();
    if($requestMethod == 'GET') {
      return $this->view($response, '@admin/accounts.logout.twig');
    }
    if($requestMethod == 'POST') {
      // Remove cookie
      setcookie($_ENV['app']['cookie']['admin'], 'expired', strtotime('now') - 1, '/');

      // Return response
      return $response->withHeader('location', '/admin/accounts/login');
    }
  }

  /**
   * Profile page
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @return Response
   */
  public function profile(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, '@admin/accounts.profile.twig', [
      'account' => AdminAccount::where('id', $this->auth('id', 'admin'))->first()
    ]);
  }

  /**
   * Change information function
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @throws HttpBadRequestException
   * @return Response
   */
  public function changeInformation(Request $request, Response $response, array $data): Response
  {
    // Check validation
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      'username'         => 'required|max:6',
      'current-password' => 'required|min:6|max:32'
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . '.');
    }

    // Get inputs
    $username = trim($inputs['username']);
    $currentPassword = trim($inputs['current-password']) . $_ENV['app']['key'];

    // Check current password matches
    $account = AdminAccount::where('id', $this->auth('id', 'admin'))->first();
    $currentPasswordMatches = password_verify($currentPassword, $account->password);
    if(!$currentPasswordMatches) {
      throw new HttpBadRequestException($request, 'Current password is invalid.');
    }

    // Update database
    $account->username = $username;
    $account->save();

    // Return response
    return $response->withHeader('location', '/admin/accounts/profile');
  }

  /**
   * Change password function
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @throws HttpBadRequestException
   * @return Response
   */
  public function changePassword(Request $request, Response $response, array $data): Response
  {
    // Check validation
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      'current-password'     => 'required|min:6|max:32',
      'new-password'         => 'required|different:current-password|min:6|max:32',
      'confirm-new-password' => 'required|same:new-password'
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . '.');
    }

    // Get inputs
    $currentPassword = trim($inputs['current-password']) . $_ENV['app']['key'];
    $newPassword = trim($inputs['new-password']) . $_ENV['app']['key'];

    // Check current password matches
    $account = AdminAccount::where('id', $this->auth('id', 'admin'))->first();
    $currentPasswordMatches = password_verify($currentPassword, $account->password);
    if(!$currentPasswordMatches) {
      throw new HttpBadRequestException($request, 'Current password is invalid.');
    }

    // Remove cookie
    setcookie($_ENV['app']['cookie']['admin'], 'expired', strtotime('now') - 1, '/');

    // Update database
    $account->unique_id = md5(uniqid(bin2hex(random_bytes(32))));
    $account->password = password_hash($newPassword, PASSWORD_BCRYPT);
    $account->save();

    // Return response
    return $response->withHeader('location', '/admin/accounts/login');
  }
}