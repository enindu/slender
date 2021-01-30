<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Category;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Categories extends Controller
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
    return $this->view($response, '@admin/categories.twig', [
      'sections'   => Section::get(),
      'categories' => Category::orderBy('id', 'desc')->take(10)->get()
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
    $allResults = count(Category::get());
    $numberOfPages = ceil($allResults / $resultsPerPage);
    if($page < 1 || $page > $numberOfPages) {
      throw new HttpNotFoundException($request);
    }

    // Return response
    $pageResults = ($page - 1) * $resultsPerPage;
    return $this->view($response, '@admin/categories.all.twig', [
      'page'            => $page,
      'number_of_pages' => $numberOfPages,
      'categories'      => Category::orderBy('id', 'desc')->skip($pageResults)->take($resultsPerPage)->get()
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
      'title'      => 'required|max:191',
      'subtitle'   => 'max:191',
      'section-id' => 'required|integer'
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . '.');
    }

    // Get inputs and files
    $title = trim($inputs['title']);
    $subtitle = trim($inputs['subtitle']);
    $sectionId = (int) trim($inputs['section-id']);
    $description = trim($inputs['description']);

    // Check section
    $section = Section::where('id', $sectionId)->get();
    if($section == null) {
      throw new HttpBadRequestException($request, 'There is no section found.');
    }

    // Get clock library
    $clock = $this->container->get('clock');

    // Update database
    Category::insert([
      'section_id'  => $sectionId,
      'slug'        => strtolower(uniqid(str_replace([' ', '/', '\\', '\'', '"'], '-', str_replace(['(', ')', '[', ']', '{', '}', ',', '.'], '', $title)) . '-')),
      'title'       => $title,
      'subtitle'    => $subtitle != '' ? $subtitle : 'false',
      'description' => $description != '' ? $description : 'false',
      'created_at'  => $clock::now(),
      'updated_at'  => $clock::now()
    ]);

    // Return response
    return $response->withHeader('location', '/admin/categories');
  }

  /**
   * Update function
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @throws HttpBadRequestException
   * @return Response
   */
  public function update(Request $request, Response $response, array $data): Response
  {
    // Check validation
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      'id'         => 'required|integer',
      'title'      => 'required|max:191',
      'subtitle'   => 'max:191',
      'section-id' => 'required|integer'
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . '.');
    }

    // Get inputs and files
    $id = (int) trim($inputs['id']);
    $title = trim($inputs['title']);
    $subtitle = trim($inputs['subtitle']);
    $sectionId = (int) trim($inputs['section-id']);
    $description = trim($inputs['description']);

    // Check category
    $category = Category::where('id', $id)->first();
    if($category == null) {
      throw new HttpBadRequestException($request, 'There is no category found.');
    }

    // Check section
    $section = Section::where('id', $sectionId)->get();
    if($section == null) {
      throw new HttpBadRequestException($request, 'There is no section found.');
    }

    // Update database
    $category->section_id = $sectionId;
    $category->slug = strtolower(uniqid(str_replace([' ', '/', '\\', '\'', '"'], '-', str_replace(['(', ')', '[', ']', '{', '}', ',', '.'], '', $title)) . '-'));
    $category->title = $title;
    $category->subtitle = $subtitle != "" ? $subtitle : "false";
    $category->description = $description != "" ? $description : "false";
    $category->save();

    // Return response
    return $response->withHeader('location', '/admin/categories/' . $id);
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

    // Check category
    $category = Category::where('id', $id)->first();
    if($category == null) {
      throw new HttpBadRequestException($request, 'There is no category found.');
    }

    // Update database
    $category->delete();

    // Return response
    return $response->withHeader('location', '/admin/categories');
  }

  /**
   * Single page
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @throws HttpNotFoundException
   * @return Response
   */
  public function single(Request $request, Response $response, array $data): Response
  {
    // Check category
    $category = Category::where('id', $data['id'])->first();
    if($category == null) {
      throw new HttpNotFoundException($request);
    }

    // Return response
    return $this->view($response, '@admin/categories.single.twig', [
      'category' => $category,
      'sections' => Section::get()
    ]);
  }
}
