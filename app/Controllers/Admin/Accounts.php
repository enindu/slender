<?php

namespace App\Controllers\Admin;

use App\Models\Admin;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controllers\Controller;

class Accounts extends Controller
{
  public function login(Request $request, Response $response, array $data): Response
  {
    $method = $request->getMethod();
    if($method == "GET") {
      return $this->view($response, "@admin/accounts.login.twig");
    }
    if($method == "POST") {
      $inputs = $request->getParsedBody();
      $validation = $this->validate($inputs, [
        "username" => "required|max:6",
        "password" => "required|min:6|max:32"
      ]);
      if($validation != null) {
        throw new HttpBadRequestException($request, reset($validation) . ".");
      }

      $username = trim($inputs["username"]);
      $password = trim($inputs["password"]) . $_ENV["app"]["key"];

      $admin = Admin::where("status", true)->where("username", $username)->first();
      if($admin == null) {
        throw new HttpBadRequestException($request, "There is no account found.");
      }

      $passwordMatches = password_verify($password, $admin->password);
      if(!$passwordMatches) {
        throw new HttpBadRequestException($request, "Password is invalid.");
      }

      setcookie($_ENV["app"]["cookie"]["admin"], $admin->unique_id, 0, "/admin", $_ENV["app"]["domain"], false, true);
      return $response->withHeader("Location", "/admin");
    }
  }
}
