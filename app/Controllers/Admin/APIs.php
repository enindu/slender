<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\API;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Crypto;
use System\Slender\Date;
use System\Slender\Text;

class APIs extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/apis.twig", [
      "apis" => API::orderBy("id", "desc")->take(10)->get()
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

    $results = count(API::get());
    $resultsPerPage = 10;
    $pageResults = ($page - 1) * $resultsPerPage;
    $pages = ceil($results / $resultsPerPage);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }

    return $this->view($response, "@admin/apis.all.twig", [
      "page"  => $page,
      "pages" => $pages,
      "apis"  => API::orderBy("id", "desc")->skip($pageResults)->take($resultsPerPage)->get()
    ]);
  }

  public function add(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "username" => "required|alpha_num|max:6"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, Text::validationMessage($validation));
    }

    $username = $inputs["username"];

    $api = API::where("username", $username)->first();
    if($api != null) {
      throw new HttpBadRequestException($request, "There is an API already using that username.");
    }

    API::insert([
      "username"   => $username,
      "token"      => Crypto::apiKey(),
      "created_at" => Date::now(),
      "updated_at" => Date::now()
    ]);

    return $response->withHeader("Location", "/admin/apis");
  }

  public function activate(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, Text::validationMessage($validation));
    }

    $id = (int) $inputs["id"];

    $api = API::where("id", $id)->first();
    if($api == null) {
      throw new HttpBadRequestException($request, "There is no API found.");
    }

    $api->status = true;
    $api->save();

    return $response->withHeader("Location", "/admin/apis");
  }

  public function deactivate(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, Text::validationMessage($validation));
    }

    $id = (int) $inputs["id"];

    $api = API::where("id", $id)->first();
    if($api == null) {
      throw new HttpBadRequestException($request, "There is no API found.");
    }

    $api->status = false;
    $api->save();

    return $response->withHeader("Location", "/admin/apis");
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

    $api = API::where("id", $id)->first();
    if($api == null) {
      throw new HttpBadRequestException($request, "There is no API found.");
    }

    $api->delete();

    return $response->withHeader("Location", "/admin/apis");
  }
}
