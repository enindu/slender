<?php

namespace System\App;

use DI\Container;
use Slim\Psr7\Response;

class Base
{
    public function __construct(protected Container $container)
    {
        $this->container->get("database");
    }

    protected function writeResponse(Response $response, int $statusCode, string $contentType, string $body): Response
    {
        $response->getBody()->write($body);
        return $response->withStatus($statusCode)->withHeader("Content-Type", $contentType);
    }

    protected function viewResponse(Response $response, string $template, array $data = [], int $statusCode = 200): Response
    {
        $view = $this->createView($template, $data);
        return $this->writeResponse($response, $statusCode, "text/html", $view);
    }

    protected function newRedirectResponse(string $path): Response
    {
        $response = new Response();
        return $this->redirectResponse($response, $path);
    }

    protected function redirectResponse(Response $response, string $path): Response
    {
        return $response->withHeader("Location", $path);
    }

    protected function newJsonResponse(array $data, int $statusCode = 200): Response
    {
        $response = new Response();
        return $this->jsonResponse($response, $data, $statusCode);
    }

    protected function jsonResponse(Response $response, array $data, int $statusCode = 200): Response
    {
        $json = json_encode($data);
        return $this->writeResponse($response, $statusCode, "application/json", $json);
    }

    protected function createView(string $template, array $data): string
    {
        $view = $this->container->get("view");
        return $view->render($template, $data);
    }

    protected function createRandomText(int $length): string
    {
        $text = str_shuffle("qwertyuiopasdfghjklzxcvbnm1234567890");
        $characters = str_split($text);
        $charactersLength = count($characters);
        $charactersLength = $charactersLength - 1;

        $randomText = "";
        for($i = 0; $i < $length; $i++) {
            $randomNumber = random_int(0, $charactersLength);
            $randomText .= $characters[$randomNumber];
        }

        return $randomText;
    }

    protected function createToken(): string
    {
        $uniqueId = uniqid(more_entropy:true);
        $uniqueId = preg_replace("/\./", "", $uniqueId);
        $uniqueIdLength = strlen($uniqueId);

        $randomTextLength = 67 - $uniqueIdLength;
        $randomText = $this->createRandomText($randomTextLength);

        $token = $uniqueId . $randomText;
        return str_shuffle($token);
    }

    protected function createSlug(string $text): string
    {
        $timestamp = time();

        $text = $text . " " . $timestamp;
        $text = trim($text);
        $text = strtolower($text);
        $text = preg_replace("/&/", " and ", $text);
        $text = preg_replace("/\W|\s+/", "-", $text);

        return preg_replace("/-+/", "-", $text);
    }

    protected function createPassword(string $password, string $algorithm = PASSWORD_ARGON2ID): array
    {
        $salt = $this->createToken();
        $characters = str_split($password . $salt . $_ENV["settings"]["key"]);
        $keys = array_keys($characters);

        shuffle($keys);

        $password = "";
        foreach($keys as $key) {
            $password .= $characters[$key];
        }

        return [
            "hash"    => password_hash($password, $algorithm),
            "salt"    => $salt,
            "pattern" => implode(".", $keys)
        ];
    }

    protected function verifyPassword(string $password, string $hash, string $salt, string $pattern): null|string
    {
        $characters = str_split($password . $salt . $_ENV["settings"]["key"]);
        $charactersLength = count($characters);
        $keys = explode(".", $pattern);
        $keysLength = count($keys);
        if($charactersLength != $keysLength) {
            return "Password is invalid.";
        }

        $password = "";
        foreach($keys as $key) {
            $password .= $characters[$key];
        }

        $passwordVerifies = password_verify($password, $hash);
        if(!$passwordVerifies) {
            return "Password is invalid.";
        }

        return null;
    }

    protected function sendEmail(string $template, array $data): null|string
    {
        $view = $this->createView($template, $data["body"]);
        $email = $this->container->get("email");

        $email->setFrom($data["from"]);
        $email->addAddress($data["to"]);

        $email->Subject = $data["subject"];
        $email->Body = $view;

        $emailSends = $email->send();
        if(!$emailSends) {
            return "Something went wrong while sending email.";
        }

        return null;
    }

    protected function validateData(array|null $data, array $rules, array $aliases = []): null|string
    {
        $dataExists = isset($data);
        if(!$dataExists) {
            return "Data is invalid.";
        }

        $validate = $this->container->get("validate");
        $validation = $validate->make($data, $rules);

        $validation->setAliases($aliases);
        $validation->validate();

        $validationFails = $validation->fails();
        if($validationFails) {
            $errors = $validation->errors()->all();
            $error = reset($errors);
            $error = trim($error);
            $error = rtrim($error, ".");
            $error = preg_replace("/\-/", " ", $error);

            return $error . ".";
        }

        return null;
    }
}
