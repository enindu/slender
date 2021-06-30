<?php

namespace App\Controllers\Admin;

use App\Models\Admin;
use App\Models\Role;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;

class Admins extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        return $this->viewResponse($response, "@admin/admins.twig", [
            "roles"  => Role::get(),
            "admins" => Admin::orderBy("id", "desc")->take(10)->get()
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

        $admins = Admin::get();
        $adminsLength = count($admins);
        $resultsLength = 10;
        $previousResultsLength = ($page - 1) * $resultsLength;

        $pages = ceil($adminsLength / $resultsLength);
        if($page < 1 || $page > $pages) {
            throw new HttpNotFoundException($request);
        }

        return $this->viewResponse($response, "@admin/admins.all.twig", [
            "page"   => $page,
            "pages"  => $pages,
            "admins" => Admin::orderBy("id", "desc")->skip($previousResultsLength)->take($resultsLength)->get()
        ]);
    }

    public function add(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "username"         => "required|alpha_num|max:6",
            "role-id"          => "required|integer",
            "password"         => "required|min:6|max:32",
            "confirm-password" => "same:password"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $username = $inputs["username"];
        $roleId = (int) $inputs["role-id"];
        $password = $inputs["password"];

        $role = Role::where("id", $roleId)->first();
        if($role == null) {
            throw new HttpBadRequestException($request, "There is no role found.");
        }

        $admin = Admin::where("username", $username)->first();
        if($admin != null) {
            throw new HttpBadRequestException($request, "There is an account already using that username.");
        }

        $password = $this->createPassword($password);
        Admin::insert([
            "role_id"    => $roleId,
            "unique_id"  => $this->createToken(),
            "username"   => $username,
            "hash"       => $password["hash"],
            "salt"       => $password["salt"],
            "pattern"    => $password["pattern"],
            "created_at" => date("Y-m-d H:i:s")
        ]);

        return $this->redirectResponse($response, "/admin/admins");
    }

    public function activate(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "id" => "required|integer"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $id = (int) $inputs["id"];

        if($id == $_SESSION["admin"]["id"]) {
            throw new HttpBadRequestException($request, "You cannot activate your own account.");
        }

        $admin = Admin::where("id", $id)->first();
        if($admin == null) {
            throw new HttpBadRequestException($request, "There is no account found.");
        }

        $admin->status = true;
        $admin->save();

        return $this->redirectResponse($response, "/admin/admins");
    }

    public function deactivate(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "id" => "required|integer"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $id = (int) $inputs["id"];

        if($id == $_SESSION["admin"]["id"]) {
            throw new HttpBadRequestException($request, "You cannot deactivate your own account.");
        }

        $admin = Admin::where("id", $id)->first();
        if($admin == null) {
            throw new HttpBadRequestException($request, "There is no account found.");
        }

        $admin->status = false;
        $admin->save();

        return $this->redirectResponse($response, "/admin/admins");
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

        if($id == $_SESSION["admin"]["id"]) {
            throw new HttpBadRequestException($request, "You cannot remove your own account.");
        }

        $admin = Admin::where("id", $id)->first();
        if($admin == null) {
            throw new HttpBadRequestException($request, "There is no account found.");
        }

        $admin->delete();
        return $this->redirectResponse($response, "/admin/admins");
    }
}
