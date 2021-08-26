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
        return $this->viewResponse($response, "@admin/sections.twig", [
            "sections" => Section::orderBy("id", "desc")->take(10)->get()
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

        return $this->viewResponse($response, "@admin/sections.all.twig", [
            "page"     => $page,
            "pages"    => $pages,
            "sections" => Section::orderBy("id", "desc")->skip($previousResultsLength)->take($resultsLength)->get()
        ]);
    }

    public function add(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "title" => "required|max:191"
        ], [
            "title" => "title"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $title = $inputs["title"];

        $section = Section::where("title", $title)->first();
        if($section != null) {
            throw new HttpBadRequestException($request, "There is a section already using that name.");
        }

        Section::insert([
            "title"      => $title,
            "created_at" => date("Y-m-d H:i:s")
        ]);

        return $this->redirectResponse($response, "/admin/sections");
    }

    public function remove(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "id" => "required|integer"
        ], [
            "id" => "ID"
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
