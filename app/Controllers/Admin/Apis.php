<?php

namespace App\Controllers\Admin;

use App\Models\Api;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;

class Apis extends Controller
{
    public function base(Request $request, Response $response, array $data): Response
    {
        return $this->viewResponse($response, "@admin/apis.twig", [
            "apis" => Api::orderBy("id", "desc")->take(10)->get()
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

        $apis = Api::get();
        $apisLength = count($apis);
        $resultsLength = 10;
        $previousResultsLength = ($page - 1) * $resultsLength;

        $pages = ceil($apisLength / $resultsLength);
        if($page < 1 || $page > $pages) {
            throw new HttpNotFoundException($request);
        }

        return $this->viewResponse($response, "@admin/apis.all.twig", [
            "page"  => $page,
            "pages" => $pages,
            "apis"  => Api::orderBy("id", "desc")->skip($previousResultsLength)->take($resultsLength)->get()
        ]);
    }

    public function add(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "username" => "required|alpha_num|max:6"
        ], [
            "username" => "username"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $username = $inputs["username"];

        $api = Api::where("username", $username)->first();
        if($api != null) {
            throw new HttpBadRequestException($request, "There is an API already using that username.");
        }

        Api::insert([
            "username"   => $username,
            "token"      => $this->createToken(),
            "created_at" => date("Y-m-d H:i:s")
        ]);

        return $this->redirectResponse($response, "/admin/apis");
    }

    public function activate(Request $request, Response $response, array $data): Response
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

        $api = Api::where("id", $id)->first();
        if($api == null) {
            throw new HttpBadRequestException($request, "There is no API found.");
        }

        $api->status = true;
        $api->save();

        return $this->redirectResponse($response, "/admin/apis");
    }

    public function deactivate(Request $request, Response $response, array $data): Response
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

        $api = Api::where("id", $id)->first();
        if($api == null) {
            throw new HttpBadRequestException($request, "There is no API found.");
        }

        $api->status = false;
        $api->save();

        return $this->redirectResponse($response, "/admin/apis");
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

        $api = Api::where("id", $id)->first();
        if($api == null) {
            throw new HttpBadRequestException($request, "There is no API found.");
        }

        $api->delete();
        return $this->redirectResponse($response, "/admin/apis");
    }
}
