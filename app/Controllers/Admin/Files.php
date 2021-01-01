<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\File;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Files extends Controller
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
    return $this->view($response, '@admin/files.twig', [
      'sections' => Section::get(),
      'files'    => File::orderBy('id', 'desc')->get()
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
    // Check input validation
    $inputs = $request->getParsedBody();
    $inputValidation = $this->validate($inputs, [
      'title'       => 'max:191',
      'subtitle'    => 'max:191',
      'section-id'  => 'required|integer',
      'description' => 'max:500'
    ]);
    if($inputValidation != null) {
      throw new HttpBadRequestException($request, reset($inputValidation) . '.');
    }

    // Check file validation
    $files = $request->getUploadedFiles();
    $fileValidation = $this->validate($_FILES, [
      'file' => 'required|uploaded_file:0,10M'
    ]);
    if($fileValidation != null) {
      throw new HttpBadRequestException($request, reset($fileValidation) . '.');
    }

    // Get inputs and files
    $title = trim($inputs['title']);
    $subtitle = trim($inputs['subtitle']);
    $sectionId = (int) trim($inputs['section-id']);
    $description = trim($inputs['description']);
    $file = $files['file'];

    // Check section
    $section = Section::where('id', $sectionId)->get();
    if($section == null) {
      throw new HttpBadRequestException($request, 'There is no section found.');
    }

    // Upload file
    $fileExtension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
    $fileName = uniqid(bin2hex(random_bytes(8))) . '.' . $fileExtension;
    $file->moveTo(__DIR__ . '/../../../uploads/files/' . $fileName);

    // Get clock library
    $clock = $this->container->get('clock');

    // Update database
    File::insert([
      'section_id'  => $sectionId,
      'title'       => $title != '' ? $title : 'false',
      'subtitle'    => $subtitle != '' ? $subtitle : 'false',
      'description' => $description != '' ? $description : 'false',
      'file'        => $fileName,
      'created_at'  => $clock::now(),
      'updated_at'  => $clock::now()
    ]);

    // Return response
    return $response->withHeader('location', '/admin/files');
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

    // Check file
    $file = File::where('id', $id)->first();
    if($file == null) {
      throw new HttpBadRequestException($request, 'There is no file found.');
    }

    // Update database
    $file->delete();

    // Return response
    return $response->withHeader('location', '/admin/files');
  }
}
