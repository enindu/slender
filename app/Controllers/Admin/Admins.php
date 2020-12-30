<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Admins extends Controller
{
  /**
   * Base page
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @throws HttpBadRequestException
   * @return Response
   */
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, '@admin/admins.twig', [
      'roles'  => Role::get(),
      'admins' => Admin::orderBy('id', 'desc')->get()
    ]);
  }

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

    // Check admin
    $admin = Admin::where('username', $username)->first();
    if($admin != null) {
      throw new HttpBadRequestException($request, 'There is an account already using that username.');
    }

    // Get clock library
    $clock = $this->container->get('clock');

    // Update database
    Admin::insert([
      'role_id'    => $roleId,
      'unique_id'  => md5(uniqid(bin2hex(random_bytes(32)))),
      'username'   => $username,
      'password'   => password_hash($password, PASSWORD_BCRYPT),
      'created_at' => $clock::now(),
      'updated_at' => $clock::now()
    ]);

    // Return response
    return $response->withHeader('location', '/admin/admins');
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

    // Check admin
    $admin = Admin::where('id', $id)->first();
    if($admin == null) {
      throw new HttpBadRequestException($request, 'There is no account found.');
    }

    // Check current account
    if($id == $this->auth('id', 'admin')) {
      throw new HttpBadRequestException($request, 'You cannot update your own account.');
    }

    // Update database
    $admin->status = true;
    $admin->save();

    // Return response
    return $response->withHeader('location', '/admin/admins');
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

    // Check admin
    $admin = Admin::where('id', $id)->first();
    if($admin == null) {
      throw new HttpBadRequestException($request, 'There is no account found.');
    }

    // Check current account
    if($id == $this->auth('id', 'admin')) {
      throw new HttpBadRequestException($request, 'You cannot update your own account.');
    }

    // Update database
    $admin->status = false;
    $admin->save();

    // Return response
    return $response->withHeader('location', '/admin/admins');
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

    // Check admin
    $admin = Admin::where('id', $id)->first();
    if($admin == null) {
      throw new HttpBadRequestException($request, 'There is no account found.');
    }

    // Check current account
    if($id == $this->auth('id', 'admin')) {
      throw new HttpBadRequestException($request, 'You cannot remove your own account.');
    }

    // Update database
    $admin->delete();

    // Return response
    return $response->withHeader('location', '/admin/admins');
  }
}
