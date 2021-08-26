<?php

namespace App\Controllers\Admin;

use App\Models\Admin;
use App\Models\Role;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use System\Slender\Controller;

class Accounts extends Controller
{
    public function login(Request $request, Response $response, array $data): Response
    {
        $method = $request->getMethod();
        if($method == "GET") {
            return $this->viewResponse($response, "@admin/accounts.login.twig");
        }

        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "username" => "required|alpha_num|max:6",
            "password" => "required|min:6|max:32"
        ], [
            "username" => "username",
            "password" => "password"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $username = $inputs["username"];
        $password = $inputs["password"];

        $admin = Admin::where("status", true)->where("username", $username)->first();
        if($admin == null) {
            throw new HttpBadRequestException($request, "There is no account found.");
        }

        $passwordError = $this->verifyPassword($password, $admin->hash, $admin->salt, $admin->pattern);
        if($passwordError != null) {
            throw new HttpBadRequestException($request, $passwordError);
        }

        $admin->unique_id = $this->createToken();
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

        return $this->redirectResponse($response, "/admin");
    }

    public function register(Request $request, Response $response, array $data): Response
    {
        $method = $request->getMethod();
        if($method == "GET") {
            return $this->viewResponse($response, "@admin/accounts.register.twig", [
                "roles" => Role::get()
            ]);
        }

        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "username"         => "required|alpha_num|max:6",
            "role-id"          => "required|integer",
            "password"         => "required|min:6|max:32",
            "confirm-password" => "same:password"
        ], [
            "username"         => "username",
            "role-id"          => "role ID",
            "password"         => "password",
            "confirm-password" => "confirm password"
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

        return $this->redirectResponse($response, "/admin/accounts/login");
    }

    public function logout(Request $request, Response $response, array $data): Response
    {
        $method = $request->getMethod();
        if($method == "GET") {
            return $this->viewResponse($response, "@admin/accounts.logout.twig");
        }

        $admin = Admin::where("status", true)->where("id", $_SESSION["admin"]["id"])->first();

        $admin->session_id = null;
        $admin->save();

        setcookie($_ENV["app"]["cookie"]["admin"], "expired", [
            "expires"  => strtotime("yesterday"),
            "path"     => "/admin",
            "domain"   => $_ENV["app"]["domain"],
            "secure"   => true,
            "httponly" => true,
            "samesite" => "Strict"
        ]);

        return $this->redirectResponse($response, "/admin/accounts/login");
    }

    public function changeInformation(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "username"         => "required|alpha_num|max:6",
            "current-password" => "required|min:6|max:32"
        ], [
            "username"         => "username",
            "current-password" => "current password"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $username = $inputs["username"];
        $currentPassword = $inputs["current-password"];

        $adminWithId = Admin::where("status", true)->where("id", $_SESSION["admin"]["id"])->first();
        $passwordError = $this->verifyPassword($currentPassword, $adminWithId->hash, $adminWithId->salt, $adminWithId->pattern);
        if($passwordError != null) {
            throw new HttpBadRequestException($request, $passwordError);
        }

        $adminWithUsername = Admin::where("status", true)->where("username", $username)->first();
        if($adminWithUsername != null && $adminWithUsername->id != $adminWithUsername) {
            throw new HttpBadRequestException($request, "There is an account already using that username.");
        }

        $adminWithId->username = $username;
        $adminWithId->save();

        return $this->redirectResponse($response, "/admin/accounts/profile");
    }

    public function changePassword(Request $request, Response $response, array $data): Response
    {
        $inputs = $request->getParsedBody();
        $validationError = $this->validateData($inputs, [
            "new-password"         => "required|different:current-password|min:6|max:32",
            "confirm-new-password" => "same:new-password",
            "current-password"     => "required|min:6|max:32"
        ], [
            "new-password"         => "new password",
            "confirm-new-password" => "confirm new password",
            "current-password"     => "current password"
        ]);
        if($validationError != null) {
            throw new HttpBadRequestException($request, $validationError);
        }

        $newPassword = $inputs["new-password"];
        $currentPassword = $inputs["current-password"];

        $admin = Admin::where("status", true)->where("id", $_SESSION["admin"]["id"])->first();
        $passwordError = $this->verifyPassword($currentPassword, $admin->hash, $admin->salt, $admin->pattern);
        if($passwordError != null) {
            throw new HttpBadRequestException($request, $passwordError);
        }

        $newPassword = $this->createPassword($newPassword);

        $admin->unique_id = $this->createToken();
        $admin->session_id = null;
        $admin->hash = $newPassword["hash"];
        $admin->salt = $newPassword["salt"];
        $admin->pattern = $newPassword["pattern"];
        $admin->save();

        setcookie($_ENV["app"]["cookie"]["admin"], "expired", [
            "expires"  => strtotime("yesterday"),
            "path"     => "/admin",
            "domain"   => $_ENV["app"]["domain"],
            "secure"   => true,
            "httponly" => true,
            "samesite" => "Strict"
        ]);

        return $this->redirectResponse($response, "/admin/accounts/login");
    }

    public function profile(Request $request, Response $response, array $data): Response
    {
        return $this->viewResponse($response, "@admin/accounts.profile.twig", [
            "admin" => Admin::where("status", true)->where("id", $_SESSION["admin"]["id"])->first()
        ]);
    }
}
