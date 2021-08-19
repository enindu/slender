<?php

namespace App\Controllers\Admin;

use App\Models\Content;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;

class Contents extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        return $this->viewResponse($response, "@admin/contents.twig", [
            "sections" => Section::get(),
            "contents" => Content::orderBy("id", "desc")->take(10)->get()
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

        $contents = Content::get();
        $contentsLength = count($contents);
        $resultsLength = 10;
        $previousResultsLength = ($page - 1) * $resultsLength;

        $pages = ceil($contentsLength / $resultsLength);
        if($page < 1 || $page > $pages) {
            throw new HttpNotFoundException($request);
        }

        return $this->viewResponse($response, "@admin/contents.all.twig", [
            "page"     => $page,
            "pages"    => $pages,
            "contents" => Content::orderBy("id", "desc")->skip($previousResultsLength)->take($resultsLength)->get()
        ]);
    }

    public function add(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "title"       => "required|max:191",
            "subtitle"    => "max:191",
            "section-id"  => "required|integer",
            "description" => "required"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $title = $inputs["title"];
        $subtitle = $inputs["subtitle"];
        $sectionId = (int) $inputs["section-id"];
        $description = $inputs["description"];

        $section = Section::where("id", $sectionId)->first();
        if($section == null) {
            throw new HttpBadRequestException($request, "There is no section found.");
        }

        Content::insert([
            "section_id"  => $sectionId,
            "title"       => $title,
            "subtitle"    => empty($subtitle) ? "N/A" : $subtitle,
            "description" => $description,
            "created_at"  => date("Y-m-d H:i:s")
        ]);

        return $this->redirectResponse($response, "/admin/contents");
    }

    public function update(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "id"          => "required|integer",
            "title"       => "required|max:191",
            "subtitle"    => "max:191",
            "section-id"  => "required|integer",
            "description" => "required"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $id = (int) $inputs["id"];
        $title = $inputs["title"];
        $subtitle = $inputs["subtitle"];
        $sectionId = (int) $inputs["section-id"];
        $description = $inputs["description"];

        $content = Content::where("id", $id)->first();
        if($content == null) {
            throw new HttpBadRequestException($request, "There is no content found.");
        }

        $section = Section::where("id", $sectionId)->first();
        if($section == null) {
            throw new HttpBadRequestException($request, "There is no section found.");
        }

        $content->section_id = $sectionId;
        $content->title = $title;
        $content->subtitle = empty($subtitle) ? "N/A" : $subtitle;
        $content->description = $description;
        $content->save();

        $path = "/admin/contents/" . $id;
        return $this->redirectResponse($response, $path);
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

        $content = Content::where("id", $id)->first();
        if($content == null) {
            throw new HttpBadRequestException($request, "There is no content found.");
        }

        $content->delete();
        return $this->redirectResponse($response, "/admin/contents");
    }

    public function single(Request $request, Response $response, array $data): Response
    {
        $content = Content::where("id", $data["id"])->first();
        if($content == null) {
            throw new HttpNotFoundException($request);
        }

        return $this->viewResponse($response, "@admin/contents.single.twig", [
            "content"  => $content,
            "sections" => Section::get()
        ]);
    }
}
