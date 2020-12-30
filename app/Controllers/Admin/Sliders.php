<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Slider;
use App\Models\Type;
use Intervention\Image\Constraint;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Sliders extends Controller
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
    return $this->view($response, '@admin/sliders.twig', [
      'types'   => Type::get(),
      'sliders' => Slider::orderBy('id', 'desc')->get()
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
      'title'    => 'max:191',
      'subtitle' => 'max:191',
      'type-id'  => 'required|integer'
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
    $file = $files['file'];

    // Check type
    $type = Type::where('id', $typeId)->get();
    if($type == null) {
      throw new HttpBadRequestException($request, 'There is no type found.');
    }

    // Upload temporary file
    $fileExtension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
    $fileName = uniqid(bin2hex(random_bytes(8))) . '.' . $fileExtension;
    $file->moveTo(__DIR__ . '/../../../temporary/' . $fileName);

    // Get image and filesystem libraries
    $image = $this->container->get('image');
    $filesystem = $this->container->get('filesystem');

    // Get raw image
    $rawImage = $image->make(__DIR__ . '/../../../temporary/' . $fileName);

    // Check raw image resolution
    $rawImageWidth = $rawImage->width();
    $rawImageHeight = $rawImage->height();
    if($rawImageWidth < 1920 || $rawImageHeight < 1080) {
      $filesystem->remove(__DIR__ . '/../../../temporary/' . $fileName);
      throw new HttpBadRequestException($request, 'Image must be at least 1920x1080.');
    }

    // Manipulate raw image
    $rawImage->resize(1920, null, function(Constraint $constraint) {
      $constraint->aspectRatio();
    })->crop(1920, 1080)->save(__DIR__ . '/../../../uploads/sliders/' . $fileName);

    // Remove temorary file
    $filesystem->remove(__DIR__ . '/../../../temporary/' . $fileName);

    // Get clock library
    $clock = $this->container->get('clock');

    // Update database
    Slider::insert([
      'type_id'    => $typeId,
      'title'      => $title != '' ? $title : 'false',
      'subtitle'   => $subtitle != '' ? $subtitle : 'false',
      'file'       => $fileName,
      'created_at' => $clock::now(),
      'updated_at' => $clock::now()
    ]);

    // Return response
    return $response->withHeader('location', '/admin/sliders');
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

    // Check slider
    $slider = Slider::where('id', $id)->first();
    if($slider == null) {
      throw new HttpBadRequestException($request, 'There is no slider found.');
    }

    // Get filesystem library
    $filesystem = $this->container->get('filesystem');

    // Remove file
    $filesystem->remove(__DIR__ . '/../../../uploads/sliders/' . $slider->file);

    // Update database
    $slider->delete();

    // Return response
    return $response->withHeader('location', '/admin/sliders');
  }
}
