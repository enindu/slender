<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Category;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Date;
use System\Slender\Text;

class Categories extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/categories.twig", [
      "sections"   => Section::get(),
      "categories" => Category::orderBy("id", "desc")->take(10)->get()
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

    $results = count(Category::get());
    $resultsPerPage = 10;
    $pageResults = ($page - 1) * $resultsPerPage;
    $pages = ceil($results / $resultsPerPage);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }
    
    return $this->view($response, "@admin/categories.all.twig", [
      "page"       => $page,
      "pages"      => $pages,
      "categories" => Category::orderBy("id", "desc")->skip($pageResults)->take($resultsPerPage)->get()
    ]);
  }

  public function add(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "title"      => "required|max:191",
      "subtitle"   => "max:191",
      "section-id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, Text::validationMessage($validation));
    }

    $title = $inputs["title"];
    $subtitle = $inputs["subtitle"];
    $sectionID = (int) $inputs["section-id"];
    $description = $inputs["description"];

    $section = Section::where("id", $sectionID)->first();
    if($section == null) {
      throw new HttpBadRequestException($request, "There is no section found.");
    }

    Category::insert([
      "section_id"  => $sectionID,
      "slug"        => Text::slug($title),
      "title"       => $title,
      "subtitle"    => $subtitle != "" ? $subtitle : "N/A",
      "description" => $description != "" ? $description : "N/A",
      "created_at"  => Date::now(),
      "updated_at"  => Date::now()
    ]);

    return $response->withHeader("Location", "/admin/categories");
  }

  public function update(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id"         => "required|integer",
      "title"      => "required|max:191",
      "subtitle"   => "max:191",
      "section-id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, Text::validationMessage($validation));
    }

    $id = (int) $inputs["id"];
    $title = $inputs["title"];
    $subtitle = $inputs["subtitle"];
    $sectionID = (int) $inputs["section-id"];
    $description = $inputs["description"];

    $category = Category::where("id", $id)->first();
    if($category == null) {
      throw new HttpBadRequestException($request, "There is no category found.");
    }

    $section = Section::where("id", $sectionID)->first();
    if($section == null) {
      throw new HttpBadRequestException($request, "There is no section found.");
    }

    $category->section_id = $sectionID;
    $category->slug = Text::slug($title);
    $category->title = $title;
    $category->subtitle = $subtitle != "" ? $subtitle : "N/A";
    $category->description = $description != "" ? $description : "N/A";
    $category->save();

    return $response->withHeader("Location", "/admin/categories/" . $id);
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

    $category = Category::where("id", $id)->first();
    if($category == null) {
      throw new HttpBadRequestException($request, "There is no category found.");
    }

    $category->delete();

    return $response->withHeader("Location", "/admin/categories");
  }

  public function single(Request $request, Response $response, array $data): Response
  {
    $category = Category::where("id", $data["id"])->first();
    if($category == null) {
      throw new HttpNotFoundException($request);
    }

    return $this->view($response, "@admin/categories.single.twig", [
      "category" => $category,
      "sections" => Section::get()
    ]);
  }
}
