<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\StringHelper;

class Subcategories extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/subcategories.twig", [
      "categories"    => Category::get(),
      "subcategories" => Subcategory::orderBy("id", "desc")->take(10)->get()
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

    $results = count(Subcategory::get());
    $resultsPerPage = 10;
    $pageResults = ($page - 1) * $resultsPerPage;
    $pages = ceil($results / $resultsPerPage);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }

    return $this->view($response, "@admin/subcategories.all.twig", [
      "page"          => $page,
      "pages"         => $pages,
      "subcategories" => Subcategory::orderBy("id", "desc")->skip($pageResults)->take($resultsPerPage)->get()
    ]);
  }

  public function add(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "title"       => "required|max:191",
      "subtitle"    => "max:191",
      "category-id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $title = trim($inputs["title"]);
    $subtitle = trim($inputs["subtitle"]);
    $categoryID = (int) trim($inputs["category-id"]);
    $description = trim($inputs["description"]);

    $category = Category::where("id", $categoryID)->first();
    if($category == null) {
      throw new HttpBadRequestException($request, "There is no category found.");
    }

    $carbon = $this->container->get("carbon");

    Subcategory::insert([
      "category_id" => $categoryID,
      "slug"        => StringHelper::createSlug($title),
      "title"       => $title,
      "subtitle"    => $subtitle != "" ? $subtitle : "N/A",
      "description" => $description != "" ? $description : "N/A",
      "created_at"  => $carbon::now(),
      "updated_at"  => $carbon::now()
    ]);

    return $response->withHeader("Location", "/admin/subcategories");
  }

  public function update(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id"          => "required|integer",
      "title"       => "required|max:191",
      "subtitle"    => "max:191",
      "category-id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $id = (int) trim($inputs["id"]);
    $title = trim($inputs["title"]);
    $subtitle = trim($inputs["subtitle"]);
    $categoryID = (int) trim($inputs["category-id"]);
    $description = trim($inputs["description"]);

    $subcategory = Subcategory::where("id", $id)->first();
    if($subcategory == null) {
      throw new HttpBadRequestException($request, "There is no subcategory found.");
    }

    $category = Category::where("id", $categoryID)->first();
    if($category == null) {
      throw new HttpBadRequestException($request, "There is no category found.");
    }

    $subcategory->category_id = $categoryID;
    $subcategory->slug = StringHelper::createSlug($title);
    $subcategory->title = $title;
    $subcategory->subtitle = $subtitle != "" ? $subtitle : "N/A";
    $subcategory->description = $description != "" ? $description : "N/A";
    $subcategory->save();

    return $response->withHeader("Location", "/admin/subcategories/" . $id);
  }

  public function remove(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $id = (int) trim($inputs["id"]);

    $subcategory = Subcategory::where("id", $id)->first();
    if($subcategory == null) {
      throw new HttpBadRequestException($request, "There is no subcategory found.");
    }

    $subcategory->delete();

    return $response->withHeader("Location", "/admin/subcategories");
  }

  public function single(Request $request, Response $response, array $data): Response
  {
    $subcategory = Subcategory::where("id", $data["id"])->first();
    if($subcategory == null) {
      throw new HttpNotFoundException($request);
    }

    return $this->view($response, "@admin/subcategories.single.twig", [
      "subcategory" => $subcategory,
      "categories"  => Category::get()
    ]);
  }
}
