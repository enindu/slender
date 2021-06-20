<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Crypto;
use System\Slender\Date;
use System\Slender\Password;
use System\Slender\Text;

class Admins extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    $roles = Role::get();
    $admins = Admin::orderBy("id", "desc")->take(10)->get();

    return $this->view($response, "@admin/admins.twig", [
      "roles"  => $roles,
      "admins" => $admins
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

    $admins = Admin::get();
    $adminsLength = count($admins);
    $resultsLength = 10;
    $previousResultsLength = ($page - 1) * $resultsLength;
    $pages = ceil($adminsLength / $resultsLength);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }

    $admins = Admin::orderBy("id", "desc")->skip($previousResultsLength)->take($resultsLength)->get();

    return $this->view($response, "@admin/admins.all.twig", [
      "page"   => $page,
      "pages"  => $pages,
      "admins" => $admins
    ]);
  }

  public function add(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "username"         => "required|alpha_num|max:6",
      "role-id"          => "required|integer",
      "password"         => "required|min:6|max:32",
      "confirm-password" => "same:password"
    ]);
    if($validation != null) {
      $validationMessage = Text::validationMessage($validation);
      throw new HttpBadRequestException($request, $validationMessage);
    }

    $username = $inputs["username"];
    $roleID = (int) $inputs["role-id"];
    $password = $inputs["password"];

    $role = Role::where("id", $roleID)->first();
    if($role == null) {
      throw new HttpBadRequestException($request, "There is no role found.");
    }

    $admin = Admin::where("username", $username)->first();
    if($admin != null) {
      throw new HttpBadRequestException($request, "There is an account already using that username.");
    }

    $uniqueID = Crypto::uniqueID();
    $password = Password::create($password);
    $createdAt = Date::now();

    Admin::insert([
      "role_id"    => $roleID,
      "unique_id"  => $uniqueID,
      "username"   => $username,
      "hash"       => $password["hash"],
      "salt"       => $password["salt"],
      "created_at" => $createdAt
    ]);

    return $response->withHeader("Location", "/admin/admins");
  }

  public function activate(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id" => "required|integer"
    ]);
    if($validation != null) {
      $validationMessage = Text::validationMessage($validation);
      throw new HttpBadRequestException($request, $validationMessage);
    }

    $id = (int) $inputs["id"];

    $admin = Admin::where("id", $id)->first();
    if($admin == null) {
      throw new HttpBadRequestException($request, "There is no account found.");
    }

    $adminID = $this->auth("id", "admin");
    if($id == $adminID) {
      throw new HttpBadRequestException($request, "You cannot activate your own account.");
    }

    $admin->status = true;
    $admin->save();

    return $response->withHeader("Location", "/admin/admins");
  }

  public function deactivate(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id" => "required|integer"
    ]);
    if($validation != null) {
      $validationMessage = Text::validationMessage($validation);
      throw new HttpBadRequestException($request, $validationMessage);
    }

    $id = (int) $inputs["id"];

    $admin = Admin::where("id", $id)->first();
    if($admin == null) {
      throw new HttpBadRequestException($request, "There is no account found.");
    }

    $adminID = $this->auth("id", "admin");
    if($id == $adminID) {
      throw new HttpBadRequestException($request, "You cannot deactivate your own account.");
    }

    $admin->status = false;
    $admin->save();

    return $response->withHeader("Location", "/admin/admins");
  }

  public function remove(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id" => "required|integer"
    ]);
    if($validation != null) {
      $validationMessage = Text::validationMessage($validation);
      throw new HttpBadRequestException($request, $validationMessage);
    }

    $id = (int) $inputs["id"];

    $admin = Admin::where("id", $id)->first();
    if($admin == null) {
      throw new HttpBadRequestException($request, "There is no account found.");
    }

    $adminID = $this->auth("id", "admin");
    if($id == $adminID) {
      throw new HttpBadRequestException($request, "You cannot remove your own account.");
    }

    $admin->delete();

    return $response->withHeader("Location", "/admin/admins");
  }
}
