<?php

namespace App\Controllers\Admin;

use App\Models\File;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;

class Files extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        $sections = Section::get();
        $files = File::orderBy("id", "desc")->take(10)->get();

        return $this->viewResponse($response, "@admin/files.twig", [
            "sections" => $sections,
            "files"    => $files
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

        $files = File::get();
        $filesLength = count($files);
        $resultsLength = 10;
        $previousResultsLength = ($page - 1) * $resultsLength;

        $pages = ceil($filesLength / $resultsLength);
        if($page < 1 || $page > $pages) {
            throw new HttpNotFoundException($request);
        }

        $files = File::orderBy("id", "desc")->skip($previousResultsLength)->take($resultsLength)->get();
        return $this->viewResponse($response, "@admin/files.all.twig", [
            "page"  => $page,
            "pages" => $pages,
            "files" => $files
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
            "file"        => "required|uploaded_file:0,10M"
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
        $filePath = __DIR__ . "/../../../uploads/files/" . $fileName;
        $file->moveTo($filePath);

        $title = empty($title) ? "N/A" : $title;
        $subtitle = empty($subtitle) ? "N/A" : $subtitle;
        $description = empty($description) ? "N/A" : $description;
        $date = date("Y-m-d H:i:s");

        File::insert([
            "section_id"  => $sectionId,
            "title"       => $title,
            "subtitle"    => $subtitle,
            "description" => $description,
            "file"        => $fileName,
            "created_at"  => $date
        ]);

        return $this->redirectResponse($response, "/admin/files");
    }

    public function remove(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "id" => "required|integer"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $id = (int) $inputs["id"];

        $file = File::where("id", $id)->first();
        if($file == null) {
            throw new HttpBadRequestException($request, "There is no file found.");
        }

        $file->delete();
        return $this->redirectResponse($response, "/admin/files");
    }
}
