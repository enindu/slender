<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\File;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Crypto;
use System\Slender\Date;
use System\Slender\Text;

class Files extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/files.twig", [
      "sections" => Section::get(),
      "files"    => File::orderBy("id", "desc")->take(10)->get()
    ]);
  }

  public function all(Request $request, Response $response, array $data): Response
  {
    $parameters = $request->getQueryParams();
    $validation = $this->validate($parameters, [
      "page" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpNotFoundException($request);
    }

    $page = (int) $parameters["page"];

    $results = count(File::get());
    $resultsPerPage = 10;
    $pageResults = ($page - 1) * $resultsPerPage;
    $pages = ceil($results / $resultsPerPage);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }
    
    return $this->view($response, "@admin/files.all.twig", [
      "page"  => $page,
      "pages" => $pages,
      "files" => File::orderBy("id", "desc")->skip($pageResults)->take($resultsPerPage)->get()
    ]);
  }

  public function add(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $files = $request->getUploadedFiles();
    $validation = $this->validate($inputs + $_FILES, [
      "title"       => "max:191",
      "subtitle"    => "max:191",
      "section-id"  => "required|integer",
      "description" => "max:500",
      "file"        => "required|uploaded_file:0,10M"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, Text::validationMessage($validation));
    }

    $title = $inputs["title"];
    $subtitle = $inputs["subtitle"];
    $sectionID = (int) $inputs["section-id"];
    $description = $inputs["description"];
    $file = $files["file"];

    $section = Section::where("id", $sectionID)->first();
    if($section == null) {
      throw new HttpBadRequestException($request, "There is no section found.");
    }

    $fileError = $file->getError();
    if($fileError != UPLOAD_ERR_OK) {
      throw new HttpInternalServerErrorException($request, "Something went wrong while uploading file.");
    }

    $fileExtension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
    $fileName = Crypto::uniqueID() . "." . $fileExtension;
    $file->moveTo(__DIR__ . "/../../../uploads/files/" . $fileName);

    File::insert([
      "section_id"  => $sectionID,
      "title"       => $title != "" ? $title : "N/A",
      "subtitle"    => $subtitle != "" ? $subtitle : "N/A",
      "description" => $description != "" ? $description : "N/A",
      "file"        => $fileName,
      "created_at"  => Date::now(),
      "updated_at"  => Date::now()
    ]);

    return $response->withHeader("Location", "/admin/files");
  }

  public function remove(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, Text::validationMessage($validation));
    }

    $id = (int) $inputs["id"];

    $file = File::where("id", $id)->first();
    if($file == null) {
      throw new HttpBadRequestException($request, "There is no file found.");
    }

    $file->delete();

    return $response->withHeader("Location", "/admin/files");
  }
}
