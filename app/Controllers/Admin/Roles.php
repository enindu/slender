<?php

namespace App\Controllers\Admin;

use App\Models\Role;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;
use System\Slender\Date;
use System\Slender\Text;

class Roles extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        $roles = Role::orderBy("id", "desc")->take(10)->get();
        return $this->viewResponse($response, "@admin/roles.twig", [
            "roles" => $roles
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

        $roles = Role::get();
        $rolesLength = count($roles);
        $resultsLength = 10;
        $previousResultsLength = ($page - 1) * $resultsLength;

        $pages = ceil($rolesLength / $resultsLength);
        if($page < 1 || $page > $pages) {
            throw new HttpNotFoundException($request);
        }

        $roles = Role::orderBy("id", "desc")->skip($previousResultsLength)->take($resultsLength)->get();
        return $this->viewResponse($response, "@admin/roles.all.twig", [
            "page"  => $page,
            "pages" => $pages,
            "roles" => $roles
        ]);
    }

    public function add(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "title" => "required|max:191"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $title = $inputs["title"];

        $role = Role::where("title", $title)->first();
        if($role != null) {
            throw new HttpBadRequestException($request, "There is a role already using that title.");
        }

        $date = date("Y-m-d H:i:s");
        Role::insert([
            "title"      => $title,
            "created_at" => $date
        ]);

        return $this->redirectResponse($response, "/admin/roles");
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

        $role = Role::where("id", $id)->first();
        if($role == null) {
            throw new HttpBadRequestException($request, "There is no role found.");
        }

        $role->delete();
        return $this->redirectResponse($response, "/admin/roles");
    }
}
