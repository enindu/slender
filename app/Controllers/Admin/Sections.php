<?php

namespace App\Controllers\Admin;

use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;

class Sections extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        $sections = Section::orderBy("id", "desc")->take(10)->get();
        return $this->viewResponse($response, "@admin/sections.twig", [
            "sections" => $sections
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

        $sections = Section::get();
        $sectionsLength = count($sections);
        $resultsLength = 10;
        $previousResultsLength = ($page - 1) * $resultsLength;

        $pages = ceil($sectionsLength / $resultsLength);
        if($page < 1 || $page > $pages) {
            throw new HttpNotFoundException($request);
        }

        $sections = Section::orderBy("id", "desc")->skip($previousResultsLength)->take($resultsLength)->get();
        return $this->viewResponse($response, "@admin/sections.all.twig", [
            "page"     => $page,
            "pages"    => $pages,
            "sections" => $sections
        ]);
    }

    public function add(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "title" => "required|max:191"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $title = $inputs["title"];

        $section = Section::where("title", $title)->first();
        if($section != null) {
            throw new HttpBadRequestException($request, "There is a section already using that name.");
        }

        $date = date("Y-m-d H:i:s");
        Section::insert([
            "title"      => $title,
            "created_at" => $date
        ]);

        return $this->redirectResponse($response, "/admin/sections");
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

        $section = Section::where("id", $id)->first();
        if($section == null) {
            throw new HttpBadRequestException($request, "There is no section found.");
        }

        $section->delete();
        return $this->redirectResponse($response, "/admin/sections");
    }
}
