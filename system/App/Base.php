<?php

namespace System\App;

use DI\Container;
use Slim\Psr7\Response;

class Base
{
    public function __construct(private Container $container)
    {
        $this->container->get("database");
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

    protected function writeResponse(Response $response, int $statusCode, string $contentType, string $body): Response
    {
        $response->getBody()->write($body);
        return $response->withStatus($statusCode)->withHeader("Content-Type", $contentType);
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

            return $error . ".";
        }

        return null;
    }

    private function createView(string $template, array $data): string
    {
        $view = $this->container->get("view");
        return $view->render($template, $data);
    }
}
