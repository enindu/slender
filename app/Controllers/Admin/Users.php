<?php

namespace App\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;

class Users extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        $roles = Role::get();
        $users = User::orderBy("id", "desc")->take(10)->get();

        return $this->viewResponse($response, "@admin/users.twig", [
            "roles" => $roles,
            "users" => $users
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

        $users = User::get();
        $usersLength = count($users);
        $resultsLength = 10;
        $previousResultsLength = ($page - 1) * $resultsLength;

        $pages = ceil($usersLength / $resultsLength);
        if($page < 1 || $page > $pages) {
            throw new HttpNotFoundException($request);
        }

        $users = User::orderBy("id", "desc")->skip($previousResultsLength)->take($resultsLength)->get();
        return $this->viewResponse($response, "@admin/users.all.twig", [
            "page"  => $page,
            "pages" => $pages,
            "users" => $users
        ]);
    }

    public function add(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "first-name"       => "required|max:191",
            "last-name"        => "required|max:191",
            "role-id"          => "required|max:191",
            "email"            => "required|email|max:191",
            "phone"            => "required|min:10|max:15",
            "password"         => "required|min:6|max:32",
            "confirm-password" => "same:password"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $firstName = $inputs["first-name"];
        $lastName = $inputs["last-name"];
        $roleId = (int) $inputs["role-id"];
        $email = $inputs["email"];
        $phone = $inputs["phone"];
        $password = $inputs["password"];

        $role = Role::where("id", $roleId)->first();
        if($role == null) {
            throw new HttpBadRequestException($request, "There is no role found.");
        }

        $user = User::where("email", $email)->orWhere("phone", $phone)->first();
        if($user != null) {
            throw new HttpBadRequestException($request, "There is an account already using that email or phone.");
        }

        $uniqueId = $this->createToken();
        $password = $this->createPassword($password);
        $date = date("Y-m-d H:i:s");

        User::insert([
            "role_id"    => $roleId,
            "unique_id"  => $uniqueId,
            "first_name" => $firstName,
            "last_name"  => $lastName,
            "email"      => $email,
            "phone"      => $phone,
            "hash"       => $password["hash"],
            "salt"       => $password["salt"],
            "pattern"    => $password["pattern"],
            "created_at" => $date
        ]);

        return $this->redirectResponse($response, "/admin/users");
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

        $user = User::where("id", $id)->first();
        if($user == null) {
            throw new HttpBadRequestException($request, "There is no account found.");
        }

        $user->status = true;
        $user->save();

        return $this->redirectResponse($response, "/admin/users");
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

        $user = User::where("id", $id)->first();
        if($user == null) {
            throw new HttpBadRequestException($request, "There is no account found.");
        }

        $user->status = false;
        $user->save();

        return $response->withHeader("Location", "/admin/users");
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

        $user = User::where("id", $id)->first();
        if($user == null) {
            throw new HttpBadRequestException($request, "There is no account found.");
        }

        $user->delete();
        return $this->redirectResponse($response, "/admin/users");
    }
}
