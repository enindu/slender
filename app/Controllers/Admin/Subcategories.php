<?php

namespace App\Controllers\Admin;

use App\Models\Category;
use App\Models\Subcategory;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;

class Subcategories extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        return $this->viewResponse($response, "@admin/subcategories.twig", [
            "categories"    => Category::get(),
            "subcategories" => Subcategory::orderBy("id", "desc")->take(10)->get()
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

        $subcategories = Subcategory::get();
        $subcategoriesLength = count($subcategories);
        $resultsLength = 10;
        $previousResultsLength = ($page - 1) * $resultsLength;

        $pages = ceil($subcategoriesLength / $resultsLength);
        if($page < 1 || $page > $pages) {
            throw new HttpNotFoundException($request);
        }

        return $this->viewResponse($response, "@admin/subcategories.all.twig", [
            "page"          => $page,
            "pages"         => $pages,
            "subcategories" => Subcategory::orderBy("id", "desc")->skip($previousResultsLength)->take($resultsLength)->get()
        ]);
    }

    public function add(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "title"       => "required|max:191",
            "subtitle"    => "max:191",
            "category-id" => "required|integer"
        ], [
            "title"       => "title",
            "subtitle"    => "subtitle",
            "category-id" => "category ID"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $title = $inputs["title"];
        $subtitle = $inputs["subtitle"];
        $categoryId = (int) $inputs["category-id"];
        $description = $inputs["description"];

        $category = Category::where("id", $categoryId)->first();
        if($category == null) {
            throw new HttpBadRequestException($request, "There is no category found.");
        }

        Subcategory::insert([
            "category_id" => $categoryId,
            "slug"        => $this->createSlug($title),
            "title"       => $title,
            "subtitle"    => empty($subtitle) ? "N/A" : $subtitle,
            "description" => empty($description) ? "N/A" : $description,
            "created_at"  => date("Y-m-d H:i:s")
        ]);

        return $this->redirectResponse($response, "/admin/subcategories");
    }

    public function update(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "id"          => "required|integer",
            "title"       => "required|max:191",
            "subtitle"    => "max:191",
            "category-id" => "required|integer"
        ], [
            "id"          => "ID",
            "title"       => "title",
            "subtitle"    => "subtitle",
            "category-id" => "category ID"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $id = (int) $inputs["id"];
        $title = $inputs["title"];
        $subtitle = $inputs["subtitle"];
        $categoryId = (int) $inputs["category-id"];
        $description = $inputs["description"];

        $subcategory = Subcategory::where("id", $id)->first();
        if($subcategory == null) {
            throw new HttpBadRequestException($request, "There is no subcategory found.");
        }

        $category = Category::where("id", $categoryId)->first();
        if($category == null) {
            throw new HttpBadRequestException($request, "There is no category found.");
        }

        $subcategory->category_id = $categoryId;
        $subcategory->slug = $this->createSlug($title);
        $subcategory->title = $title;
        $subcategory->subtitle = empty($subtitle) ? "N/A" : $subtitle;
        $subcategory->description = empty($description) ? "N/A" : $description;
        $subcategory->save();

        $path = "/admin/subcategories/" . $id;
        return $this->redirectResponse($response, $path);
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

        $subcategory = Subcategory::where("id", $id)->first();
        if($subcategory == null) {
            throw new HttpBadRequestException($request, "There is no subcategory found.");
        }

        $subcategory->delete();
        return $this->redirectResponse($response, "/admin/subcategories");
    }

    public function single(Request $request, Response $response, array $data): Response
    {
        $subcategory = Subcategory::where("id", $data["id"])->first();
        if($subcategory == null) {
            throw new HttpNotFoundException($request);
        }

        return $this->viewResponse($response, "@admin/subcategories.single.twig", [
            "subcategory" => $subcategory,
            "categories"  => Category::get()
        ]);
    }
}
