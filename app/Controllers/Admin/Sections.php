<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Sections extends Controller
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
    return $this->view($response, '@admin/sections.twig', [
      'sections' => Section::orderBy('id', 'desc')->get()
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

    // Check section
    $section = Section::where('title', $title)->first();
    if($section != null) {
      throw new HttpBadRequestException($request, 'There is a section already using that title.');
    }

    // Get clock library
    $clock = $this->container->get('clock');

    // Update database
    Section::insert([
      'title'      => $title,
      'created_at' => $clock::now(),
      'updated_at' => $clock::now()
    ]);

    // Return response
    return $response->withHeader('location', '/admin/sections');
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

    // Check section
    $section = Section::where('id', $id)->first();
    if($section == null) {
      throw new HttpBadRequestException($request, 'There is no section found.');
    }

    // Update database
    $section->delete();

    // Return response
    return $response->withHeader('location', '/admin/sections');
  }
}
