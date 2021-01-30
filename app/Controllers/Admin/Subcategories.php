<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Subcategories extends Controller
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
    return $this->view($response, '@admin/subcategories.twig', [
      'categories'    => Category::get(),
      'subcategories' => Subcategory::orderBy('id', 'desc')->take(10)->get()
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
    $allResults = count(Subcategory::get());
    $numberOfPages = ceil($allResults / $resultsPerPage);
    if($page < 1 || $page > $numberOfPages) {
      throw new HttpNotFoundException($request);
    }

    // Return response
    $pageResults = ($page - 1) * $resultsPerPage;
    return $this->view($response, '@admin/subcategories.all.twig', [
      'page'            => $page,
      'number_of_pages' => $numberOfPages,
      'subcategories'   => Subcategory::orderBy('id', 'desc')->skip($pageResults)->take($resultsPerPage)->get()
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
      'title'       => 'required|max:191',
      'subtitle'    => 'max:191',
      'category-id' => 'required|integer'
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . '.');
    }

    // Get inputs and files
    $title = trim($inputs['title']);
    $subtitle = trim($inputs['subtitle']);
    $categoryId = (int) trim($inputs['category-id']);
    $description = trim($inputs['description']);

    // Check category
    $category = Category::where('id', $categoryId)->first();
    if($category == null) {
      throw new HttpBadRequestException($request, 'There is no category found.');
    }

    // Get clock library
    $clock = $this->container->get('clock');

    // Update database
    Subcategory::insert([
      'category_id' => $categoryId,
      'slug'        => strtolower(uniqid(str_replace([' ', '/', '\\', '\'', '"'], '-', str_replace(['(', ')', '[', ']', '{', '}', ',', '.'], '', $title)) . '-')),
      'title'       => $title,
      'subtitle'    => $subtitle != '' ? $subtitle : 'false',
      'description' => $description != '' ? $description : 'false',
      'created_at'  => $clock::now(),
      'updated_at'  => $clock::now()
    ]);

    // Return response
    return $response->withHeader('location', '/admin/subcategories');
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
      'id'          => 'required|integer',
      'title'       => 'required|max:191',
      'subtitle'    => 'max:191',
      'category-id' => 'required|integer'
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . '.');
    }

    // Get inputs and files
    $id = (int) trim($inputs['id']);
    $title = trim($inputs['title']);
    $subtitle = trim($inputs['subtitle']);
    $categoryId = (int) trim($inputs['category-id']);
    $description = trim($inputs['description']);

    // Check subcategory
    $subcategory = Subcategory::where('id', $id)->first();
    if($subcategory == null) {
      throw new HttpBadRequestException($request, 'There is no subcategory found.');
    }

    // Check category
    $category = Category::where('id', $categoryId)->first();
    if($category == null) {
      throw new HttpBadRequestException($request, 'There is no category found.');
    }

    // Update database
    $subcategory->category_id = $categoryId;
    $subcategory->slug = strtolower(uniqid(str_replace([' ', '/', '\\', '\'', '"'], '-', str_replace(['(', ')', '[', ']', '{', '}', ',', '.'], '', $title)) . '-'));
    $subcategory->title = $title;
    $subcategory->subtitle = $subtitle != "" ? $subtitle : "false";
    $subcategory->description = $description != "" ? $description : "false";
    $subcategory->save();

    // Return response
    return $response->withHeader('location', '/admin/subcategories/' . $id);
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

    // Check subcategory
    $subcategory = Subcategory::where('id', $id)->first();
    if($subcategory == null) {
      throw new HttpBadRequestException($request, 'There is no subcategory found.');
    }

    // Update database
    $subcategory->delete();

    // Return response
    return $response->withHeader('location', '/admin/subcategories');
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
    // Check subcategory
    $subcategory = Subcategory::where('id', $data['id'])->first();
    if($subcategory == null) {
      throw new HttpNotFoundException($request);
    }

    // Return response
    return $this->view($response, '@admin/subcategories.single.twig', [
      'subcategory' => $subcategory,
      'categories'  => Category::get()
    ]);
  }
}
