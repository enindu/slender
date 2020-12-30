<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Image;
use App\Models\Type;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Images extends Controller
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
    return $this->view($response, '@admin/images.twig', [
      'types'  => Type::get(),
      'images' => Image::orderBy('id', 'desc')->get()
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
      'type-id'     => 'required|integer',
      'description' => 'max:500'
    ]);
    if($inputValidation != null) {
      throw new HttpBadRequestException($request, reset($inputValidation) . '.');
    }

    // Check file validation
    $files = $request->getUploadedFiles();
    $fileValidation = $this->validate($_FILES, [
      'file' => 'required|uploaded_file:0,5M,jpeg,png'
    ]);
    if($fileValidation != null) {
      throw new HttpBadRequestException($request, reset($fileValidation) . '.');
    }

    // Get inputs and files
    $title = trim($inputs['title']);
    $subtitle = trim($inputs['subtitle']);
    $typeId = (int) trim($inputs['type-id']);
    $description = trim($inputs['description']);
    $file = $files['file'];

    // Check type
    $type = Type::where('id', $typeId)->get();
    if($type == null) {
      throw new HttpBadRequestException($request, 'There is no type found.');
    }

    // Upload file
    $fileExtension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
    $fileName = uniqid(bin2hex(random_bytes(8))) . '.' . $fileExtension;
    $file->moveTo(__DIR__ . '/../../../uploads/images/' . $fileName);

    // Get clock library
    $clock = $this->container->get('clock');

    // Update database
    Image::insert([
      'type_id'     => $typeId,
      'title'       => $title != '' ? $title : 'false',
      'subtitle'    => $subtitle != '' ? $subtitle : 'false',
      'description' => $description != '' ? $description : 'false',
      'file'        => $fileName,
      'created_at'  => $clock::now(),
      'updated_at'  => $clock::now()
    ]);

    // Return response
    return $response->withHeader('location', '/admin/images');
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

    // Check image
    $image = Image::where('id', $id)->first();
    if($image == null) {
      throw new HttpBadRequestException($request, 'There is no image found.');
    }

    // Get filesystem library
    $filesystem = $this->container->get('filesystem');

    // Remove file
    $filesystem->remove(__DIR__ . '/../../../uploads/images/' . $image->file);

    // Update database
    $image->delete();

    // Return response
    return $response->withHeader('location', '/admin/images');
  }
}
