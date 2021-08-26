<?php

namespace App\Controllers\Admin;

use App\Models\Image;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;

class Images extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        return $this->viewResponse($response, "@admin/images.twig", [
            "sections" => Section::get(),
            "images"   => Image::orderBy("id", "desc")->take(10)->get()
        ]);
    }

    public function all(Request $request, Response $response, array $data): Response
    {
        $parameters = $request->getQueryParams();
        $validationError = $this->validateData($parameters, [
            "page" => "required|integer"
        ]);
        if($validationError != null) {
            throw new HttpNotFoundException($request);
        }

        $page = (int) $parameters["page"];

        $images = Image::get();
        $imagesLength = count($images);
        $resultsLength = 10;
        $previousResultsLength = ($page - 1) * $resultsLength;

        $pages = ceil($imagesLength / $resultsLength);
        if($page < 1 || $page > $pages) {
            throw new HttpNotFoundException($request);
        }

        return $this->viewResponse($response, "@admin/images.all.twig", [
            "page"   => $page,
            "pages"  => $pages,
            "images" => Image::orderBy("id", "desc")->skip($previousResultsLength)->take($resultsLength)->get()
        ]);
    }

    public function add(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $validationError = $this->validateData($inputs + $_FILES, [
            "title"       => "max:191",
            "subtitle"    => "max:191",
            "section-id"  => "required|integer",
            "description" => "max:500",
            "file"        => "required|uploaded_file:0,5M,jpeg,png,webp"
        ], [
            "title"       => "title",
            "subtitle"    => "subtitle",
            "section-id"  => "section ID",
            "description" => "description",
            "file"        => "file"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $title = $inputs["title"];
        $subtitle = $inputs["subtitle"];
        $sectionId = (int) $inputs["section-id"];
        $description = $inputs["description"];
        $file = $files["file"];

        $section = Section::where("id", $sectionId)->first();
        if($section == null) {
            throw new HttpBadRequestException($request, "There is no section found.");
        }

        $fileError = $file->getError();
        if($fileError != UPLOAD_ERR_OK) {
            throw new HttpInternalServerErrorException($request, "Something went wrong while uploading file.");
        }

        $fileName = $file->getClientFilename();
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        $token = $this->createToken();
        $fileName = $token . "." . $fileExtension;
        $filePath = __DIR__ . "/../../../uploads/images/" . $fileName;
        $file->moveTo($filePath);

        Image::insert([
            "section_id"  => $sectionId,
            "title"       => empty($title) ? "N/A" : $title,
            "subtitle"    => empty($subtitle) ? "N/A" : $subtitle,
            "description" => empty($description) ? "N/A" : $description,
            "file"        => $fileName,
            "created_at"  => date("Y-m-d H:i:s")
        ]);

        return $this->redirectResponse($response, "/admin/images");
    }

    public function remove(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "id" => "required|integer"
        ], [
            "id" => "id"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $id = (int) $inputs["id"];

        $image = Image::where("id", $id)->first();
        if($image == null) {
            throw new HttpBadRequestException($request, "There is no image found.");
        }

        $image->delete();
        return $this->redirectResponse($response, "/admin/images");
    }
}
