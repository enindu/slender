<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Crypto;
use System\Slender\Date;
use System\Slender\Password;
use System\Slender\Text;

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
        "username" => "required|alpha_num|max:6",
        "password" => "required|min:6|max:32"
      ]);
      if($validation != null) {
        $validationMessage = Text::validationMessage($validation);
        throw new HttpBadRequestException($request, $validationMessage);
      }

      $username = $inputs["username"];
      $password = $inputs["password"];

      $admin = Admin::where("status", true)->where("username", $username)->first();
      if($admin == null) {
        throw new HttpBadRequestException($request, "There is no account found.");
      }

      $passwordMatches = Password::verify($password, $admin->hash, $admin->salt);
      if(!$passwordMatches) {
        throw new HttpBadRequestException($request, "Password is invalid.");
      }

      $admin->unique_id = Crypto::uniqueID();
      $admin->session_id = session_id();
      $admin->save();

      setcookie($_ENV["app"]["cookie"]["admin"], $admin->unique_id, [
        "expires"  => 0,
        "path"     => "/admin",
        "domain"   => $_ENV["app"]["domain"],
        "secure"   => true,
        "httponly" => true,
        "samesite" => "Strict"
      ]);

      return $response->withHeader("Location", "/admin");
    }
  }

  public function register(Request $request, Response $response, array $data): Response
  {
    $method = $request->getMethod();
    if($method == "GET") {
      $roles = Role::get();

      return $this->view($response, "@admin/accounts.register.twig", [
        "roles" => $roles
      ]);
    }
    if($method == "POST") {
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

      return $response->withHeader("Location", "/admin/accounts/login");
    }
  }

  public function logout(Request $request, Response $response, array $data): Response
  {
    $method = $request->getMethod();
    if($method == "GET") {
      return $this->view($response, "@admin/accounts.logout.twig");
    }
    if($method == "POST") {
      $admin = Admin::where("status", true)->where("id", $this->auth("id", "admin"))->first();
      if($admin == null) {
        throw new HttpBadRequestException($request, "You are not logged in.");
      }
      
      $admin->session_id = null;
      $admin->save();

      $expires = strtotime("now") - 1;
      setcookie($_ENV["app"]["cookie"]["admin"], "expired", [
        "expires"  => $expires,
        "path"     => "/admin",
        "domain"   => $_ENV["app"]["domain"],
        "secure"   => true,
        "httponly" => true,
        "samesite" => "Strict"
      ]);

      return $response->withHeader("Location", "/admin/accounts/login");
    }
  }

  public function changeInformation(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "username"         => "required|alpha_num|max:6",
      "current-password" => "required|min:6|max:32"
    ]);
    if($validation != null) {
      $validationMessage = Text::validationMessage($validation);
      throw new HttpBadRequestException($request, $validationMessage);
    }

    $username = $inputs["username"];
    $currentPassword = $inputs["current-password"];

    $adminID = $this->auth("id", "admin");
    $admin = Admin::where("status", true)->where("id", $adminID)->first();
    $currentPasswordMatches = Password::verify($currentPassword, $admin->hash, $admin->salt);
    if(!$currentPasswordMatches) {
      throw new HttpBadRequestException($request, "Current password is invalid.");
    }

    $admin = Admin::where("status", true)->where("username", $username)->first();
    if($admin != null && $admin->id != $adminID) {
      throw new HttpBadRequestException($request, "There is an account already using that username.");
    }

    $admin->username = $username;
    $admin->save();

    return $response->withHeader("Location", "/admin/accounts/profile");
  }

  public function changePassword(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "new-password"         => "required|different:current-password|min:6|max:32",
      "confirm-new-password" => "same:new-password",
      "current-password"     => "required|min:6|max:32"
    ]);
    if($validation != null) {
      $validationMessage = Text::validationMessage($validation);
      throw new HttpBadRequestException($request, $validationMessage);
    }

    $newPassword = $inputs["new-password"];
    $currentPassword = $inputs["current-password"];

    $adminID = $this->auth("id", "admin");
    $admin = Admin::where("status", true)->where("id", $adminID)->first();
    $currentPasswordMatches = Password::verify($currentPassword, $admin->hash, $admin->salt);
    if(!$currentPasswordMatches) {
      throw new HttpBadRequestException($request, "Current password is invalid.");
    }

    $newPassword = Password::create($newPassword);

    $admin->unique_id = Crypto::uniqueID();
    $admin->session_id = null;
    $admin->hash = $newPassword["hash"];
    $admin->salt = $newPassword["salt"];
    $admin->save();

    $expires = strtotime("now") - 1;
    setcookie($_ENV["app"]["cookie"]["admin"], "expired", [
      "expires"  => $expires,
      "path"     => "/admin",
      "domain"   => $_ENV["app"]["domain"],
      "secure"   => true,
      "httponly" => true,
      "samesite" => "Strict"
    ]);

    return $response->withHeader("Location", "/admin/accounts/login");
  }

  public function profile(Request $request, Response $response, array $data): Response
  {
    $adminID = $this->auth("id", "admin");
    $admin = Admin::where("status", true)->where("id", $adminID)->first();

    return $this->view($response, "@admin/accounts.profile.twig", [
      "admin" => $admin
    ]);
  }
}
