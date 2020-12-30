<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Role;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Roles extends Controller
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
    return $this->view($response, '@admin/roles.twig', [
      'roles' => Role::orderBy('id', 'desc')->get()
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
      'title' => 'required|max:191'
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . '.');
    }

    // Get inputs
    $title = trim($inputs['title']);

    // Check role
    $role = Role::where('title', $title)->first();
    if($role != null) {
      throw new HttpForbiddenException($request, 'There is a role already using that title.');
    }

    // Get clock library
    $clock = $this->container->get('clock');

    // Update database
    Role::insert([
      'title'      => $title,
      'created_at' => $clock::now(),
      'updated_at' => $clock::now()
    ]);

    // Return response
    return $response->withHeader('location', '/admin/roles');
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

    // Check role
    $role = Role::where('id', $id)->first();
    if($role == null) {
      throw new HttpForbiddenException($request, 'There is no role found.');
    }

    // Update database
    $role->delete();

    // Return response
    return $response->withHeader('location', '/admin/roles');
  }
}
