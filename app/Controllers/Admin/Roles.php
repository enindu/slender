<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Role;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
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
      'roles' => Role::orderBy('id', 'desc')->take(10)->get()
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
    $allResults = count(Role::get());
    $numberOfPages = ceil($allResults / $resultsPerPage);
    if($page < 1 || $page > $numberOfPages) {
      throw new HttpNotFoundException($request);
    }

    // Return response
    $pageResults = ($page - 1) * $resultsPerPage;
    return $this->view($response, '@admin/roles.all.twig', [
      'page'            => $page,
      'number_of_pages' => $numberOfPages,
      'roles'           => Role::orderBy('id', 'desc')->skip($pageResults)->take($resultsPerPage)->get()
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
      throw new HttpBadRequestException($request, 'There is a role already using that title.');
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
      throw new HttpBadRequestException($request, 'There is no role found.');
    }

    // Update database
    $role->delete();

    // Return response
    return $response->withHeader('location', '/admin/roles');
  }
}
