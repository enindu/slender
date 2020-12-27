<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Role;
use App\Models\UserAccount;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Users extends Controller
{
  /**
   * Add function
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @throws HttpBadRequestException
   * @return Response
   */
  public function add(Request $request, Response $response, array $data): Response
  {
    // Check validation
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      'first-name'       => 'required|max:191',
      'last-name'        => 'required|max:191',
      'role-id'          => 'required|max:191',
      'email'            => 'required|email|max:191',
      'phone'            => 'required|min:10|max:10',
      'password'         => 'required|min:6|max:32',
      'confirm-password' => 'required|same:password'
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . '.');
    }

    // Get inputs
    $firstName = trim($inputs['first-name']);
    $lastName = trim($inputs['last-name']);
    $roleId = (int) trim($inputs['role-id']);
    $email = trim($inputs['email']);
    $phone = trim($inputs['phone']);
    $password = trim($inputs['password']) . $_ENV['app']['key'];

    // Check role
    $role = Role::where('id', $roleId)->first();
    if($role == null) {
      throw new HttpBadRequestException($request, 'There is no role found.');
    }

    // Check account
    $account = UserAccount::where('email', $email)->orWhere('phone', $phone)->first();
    if($account != null) {
      throw new HttpBadRequestException($request, 'There is an account already using that email or phone.');
    }

    // Get clock library
    $clock = $this->container->get('clock');

    // Update database
    UserAccount::insert([
      'role_id'    => $roleId,
      'unique_id'  => md5(uniqid(bin2hex(random_bytes(32)))),
      'first_name' => $firstName,
      'last_name'  => $lastName,
      'email'      => $email,
      'phone'      => $phone,
      'password'   => password_hash($password, PASSWORD_BCRYPT),
      'created_at' => $clock::now(),
      'updated_at' => $clock::now()
    ]);

    // Return response
    return $response->withHeader('location', '/admin/users');
  }

  /**
   * Activate function
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @throws HttpBadRequestException
   * @return Response
   */
  public function activate(Request $request, Response $response, array $data): Response
  {
    // Check validation
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      'id' => 'required|integer'
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . '.');
    }

    // Get inputs
    $id = (int) trim($inputs['id']);

    // Check account
    $account = UserAccount::where('id', $id)->first();
    if($account == null) {
      throw new HttpBadRequestException($request, 'There is no account found.');
    }

    // Update database
    $account->status = true;
    $account->save();

    // Return response
    return $response->withHeader('location', '/admin/users');
  }

  /**
   * Deactivate function
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @throws HttpBadRequestException
   * @return Response
   */
  public function deactivate(Request $request, Response $response, array $data): Response
  {
    // Check validation
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      'id' => 'required|integer'
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . '.');
    }

    // Get inputs
    $id = (int) trim($inputs['id']);

    // Check account
    $account = UserAccount::where('id', $id)->first();
    if($account == null) {
      throw new HttpBadRequestException($request, 'There is no account found.');
    }

    // Update database
    $account->status = false;
    $account->save();

    // Return response
    return $response->withHeader('location', '/admin/users');
  }

  /**
   * Remove function
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @throws HttpBadRequestException
   * @return Response
   */
  public function remove(Request $request, Response $response, array $data): Response
  {
    // Check validation
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      'id' => 'required|integer'
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . '.');
    }

    // Get inputs
    $id = (int) trim($inputs['id']);

    // Check account
    $account = UserAccount::where('id', $id)->first();
    if($account == null) {
      throw new HttpBadRequestException($request, 'There is no account found.');
    }

    // Update database
    $account->delete();

    // Return response
    return $response->withHeader('location', '/admin/users');
  }
}
