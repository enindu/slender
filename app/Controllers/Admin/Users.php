<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Users extends Controller
{
  /**
   * Base page
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @return Response
   */
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, '@admin/users.twig', [
      'roles' => Role::get(),
      'users' => User::orderBy('id', 'desc')->take(10)->get()
    ]);
  }

  /**
   * All page
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @throws HttpNotFoundException
   * @return Response
   */
  public function all(Request $request, Response $response, array $data): Response
  {
    // Check validation
    $parameters = $request->getQueryParams();
    $validation = $this->validate($parameters, [
      'page' => 'required|integer'
    ]);
    if($validation != null) {
      throw new HttpNotFoundException($request);
    }

    // Get parameters
    $page = (int) trim($parameters['page']);

    // Check page
    $resultsPerPage = 10;
    $allResults = count(User::get());
    $numberOfPages = ceil($allResults / $resultsPerPage);
    if($page < 1 || $page > $numberOfPages) {
      throw new HttpNotFoundException($request);
    }

    // Return response
    $pageResults = ($page - 1) * $resultsPerPage;
    return $this->view($response, '@admin/users.all.twig', [
      'page'            => $page,
      'number_of_pages' => $numberOfPages,
      'users'           => User::orderBy('id', 'desc')->skip($pageResults)->take($resultsPerPage)->get()
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

    // Check user
    $user = User::where('email', $email)->orWhere('phone', $phone)->first();
    if($user != null) {
      throw new HttpBadRequestException($request, 'There is an account already using that email or phone.');
    }

    // Get clock library
    $clock = $this->container->get('clock');

    // Update database
    User::insert([
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

    // Check user
    $user = User::where('id', $id)->first();
    if($user == null) {
      throw new HttpBadRequestException($request, 'There is no account found.');
    }

    // Update database
    $user->status = true;
    $user->save();

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

    // Check user
    $user = User::where('id', $id)->first();
    if($user == null) {
      throw new HttpBadRequestException($request, 'There is no account found.');
    }

    // Update database
    $user->status = false;
    $user->save();

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

    // Check user
    $user = User::where('id', $id)->first();
    if($user == null) {
      throw new HttpBadRequestException($request, 'There is no account found.');
    }

    // Update database
    $user->delete();

    // Return response
    return $response->withHeader('location', '/admin/users');
  }
}
