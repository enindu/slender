<?php

namespace App\Controllers\Admin;

use App\Models\Category;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;

class Categories extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        return $this->viewResponse($response, "@admin/categories.twig", [
            "sections"   => Section::get(),
            "categories" => Category::orderBy("id", "desc")->take(10)->get()
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

        $categories = Category::get();
        $categoriesLength = count($categories);
        $resultsLength = 10;
        $previousResultsLength = ($page - 1) * $resultsLength;

        $pages = ceil($categoriesLength / $resultsLength);
        if($page < 1 || $page > $pages) {
            throw new HttpNotFoundException($request);
        }

        return $this->viewResponse($response, "@admin/categories.all.twig", [
            "page"       => $page,
            "pages"      => $pages,
            "categories" => Category::orderBy("id", "desc")->skip($previousResultsLength)->take($resultsLength)->get()
        ]);
    }

    public function add(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "title"      => "required|max:191",
            "subtitle"   => "max:191",
            "section-id" => "required|integer"
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

        Category::insert([
            "section_id"  => $sectionId,
            "slug"        => $this->createSlug($title),
            "title"       => $title,
            "subtitle"    => empty($subtitle) ? "N/A" : $subtitle,
            "description" => empty($description) ? "N/A" : $description,
            "created_at"  => date("Y-m-d H:i:s")
        ]);

        return $this->redirectResponse($response, "/admin/categories");
    }

    public function update(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "id"         => "required|integer",
            "title"      => "required|max:191",
            "subtitle"   => "max:191",
            "section-id" => "required|integer"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $id = (int) $inputs["id"];
        $title = $inputs["title"];
        $subtitle = $inputs["subtitle"];
        $sectionId = (int) $inputs["section-id"];
        $description = $inputs["description"];

        $category = Category::where("id", $id)->first();
        if($category == null) {
            throw new HttpBadRequestException($request, "There is no category found.");
        }

        $section = Section::where("id", $sectionId)->first();
        if($section == null) {
            throw new HttpBadRequestException($request, "There is no section found.");
        }

        $category->section_id = $sectionId;
        $category->slug = $this->createSlug($title);
        $category->title = $title;
        $category->subtitle = empty($subtitle) ? "N/A" : $subtitle;
        $category->description = empty($description) ? "N/A" : $description;
        $category->save();

        $path = "/admin/categories/" . $id;
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

        $category = Category::where("id", $id)->first();
        if($category == null) {
            throw new HttpBadRequestException($request, "There is no category found.");
        }

        $category->delete();
        return $this->redirectResponse($response, "/admin/categories");
    }

    public function single(Request $request, Response $response, array $data): Response
    {
        $category = Category::where("id", $data["id"])->first();
        if($category == null) {
            throw new HttpNotFoundException($request);
        }

        return $this->viewResponse($response, "@admin/categories.single.twig", [
            "category" => $category,
            "sections" => Section::get()
        ]);
    }
}
