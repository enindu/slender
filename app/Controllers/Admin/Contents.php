<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Content;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Contents extends Controller
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
    return $this->view($response, '@admin/contents.twig', [
      'sections' => Section::get(),
      'contents' => Content::orderBy('id', 'desc')->get()
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
      'section-id'  => 'required|integer',
      'description' => 'required|max:500'
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
    Content::insert([
      'section_id'  => $sectionId,
      'title'       => $title,
      'subtitle'    => $subtitle != '' ? $subtitle : 'false',
      'description' => $description,
      'created_at'  => $clock::now(),
      'updated_at'  => $clock::now()
    ]);

    // Return response
    return $response->withHeader('location', '/admin/contents');
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
      'section-id'  => 'required|integer',
      'description' => 'required|max:500'
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

    // Check content
    $content = Content::where('id', $id)->first();
    if($content == null) {
      throw new HttpBadRequestException($request, 'There is no content found.');
    }

    // Check section
    $section = Section::where('id', $sectionId)->get();
    if($section == null) {
      throw new HttpBadRequestException($request, 'There is no section found.');
    }

    // Update database
    $content->section_id = $sectionId;
    $content->title = $title;
    $content->subtitle = $subtitle != "" ? $subtitle : "false";
    $content->description = $description;
    $content->save();

    // Return response
    return $response->withHeader('location', '/admin/contents/' . $id);
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

    // Check content
    $content = Content::where('id', $id)->first();
    if($content == null) {
      throw new HttpBadRequestException($request, 'There is no content found.');
    }

    // Update database
    $content->delete();

    // Return response
    return $response->withHeader('location', '/admin/contents');
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
    // Check content
    $content = Content::where('id', $data['id'])->first();
    if($content == null) {
      throw new HttpNotFoundException($request);
    }

    // Return response
    return $this->view($response, '@admin/contents.single.twig', [
      'content'  => $content,
      'sections' => Section::get()
    ]);
  }
}
