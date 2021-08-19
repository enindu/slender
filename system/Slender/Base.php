<?php

namespace System\Slender;

use DI\Container;
use Slim\Psr7\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class Base
{
    public function __construct(private Container $container)
    {
        $this->container->get("eloquent");
    }

    protected function writeResponse(Response $response, int $statusCode, string $contentType, string $body): Response
    {
        $response->getBody()->write($body);
        return $response->withStatus($statusCode)->withHeader("Content-Type", $contentType);
    }

    protected function redirectResponse(Response $response, string $path): Response
    {
        return $response->withHeader("Location", $path);
    }

    protected function newRedirectResponse(string $path): Response
    {
        $response = new Response();
        return $response->withHeader("Location", $path);
    }

    protected function viewResponse(Response $response, string $template, array $data = [], int $statusCode = 200): Response
    {
        $view = $this->createView($template, $data);
        return $this->writeResponse($response, $statusCode, "text/html", $view);
    }

    protected function jsonResponse(Response $response, array $data, int $statusCode = 200): Response
    {
        $json = json_encode($data);
        return $this->writeResponse($response, $statusCode, "application/json", $json);
    }

    protected function newJsonResponse(array $data, int $statusCode = 200): Response
    {
        $response = new Response();
        return $this->jsonResponse($response, $data, $statusCode);
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

    protected function createSlug(string $text): string
    {
        $timestamp = time();

        $text = $text . " " . $timestamp;
        $text = trim($text);
        $text = strtolower($text);
        $text = str_replace("&", " and ", $text);
        $text = preg_replace("/\W|\s+/", "-", $text);
        $text = preg_replace("/-+/", "-", $text);

        return $text;
    }

    protected function createToken(): string
    {
        $uniqueId = uniqid(more_entropy:true);
        $uniqueId = str_replace(".", "", $uniqueId);
        $uniqueIdLength = strlen($uniqueId);

        $randomTextLength = 67 - $uniqueIdLength;
        $randomText = $this->createRandomText($randomTextLength);

        $token = $uniqueId . $randomText;
        return str_shuffle($token);
    }

    protected function createPassword(string $password, string $algorithm = PASSWORD_ARGON2ID): array
    {
        $salt = $this->createToken();
        $characters = str_split($password . $salt . $_ENV["app"]["key"]);
        $keys = array_keys($characters);
        shuffle($keys);

        $password = "";
        foreach($keys as $key) {
            $password .= $characters[$key];
        }

        $hash = password_hash($password, $algorithm);
        $pattern = implode(".", $keys);

        return [
            "hash"    => $hash,
            "salt"    => $salt,
            "pattern" => $pattern
        ];
    }

    protected function verifyPassword(string $password, string $hash, string $salt, string $pattern): null|string
    {
        $characters = str_split($password . $salt . $_ENV["app"]["key"]);
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

    protected function validateData(array|null $data, array $rules): null|string
    {
        $dataExists = isset($data);
        $dataEmpty = empty($data);
        if(!$dataExists || $dataEmpty) {
            return "Data is invalid.";
        }

        $validation = $this->container->get("validation");
        $validation = $validation->validate($data, $rules);
        $validationFails = $validation->fails();
        if($validationFails) {
            $errors = $validation->errors()->all();
            $error = reset($errors);
            $error = trim($error);
            $error = strtolower($error);
            $sentences = explode(". ", $error);

            $error = "";
            foreach($sentences as $sentence) {
                $sentenceEmpty = empty($sentence);
                if($sentenceEmpty) {
                    continue;
                }

                $sentence = trim($sentence);
                $sentence = trim($sentence, ".");
                $sentence = ucfirst($sentence);
                $error .= $sentence . ".";
            }

            return str_replace("-", " ", $error);
        }

        return null;
    }

    protected function sendEmail(string $template, array $data): null|string
    {
        $view = $this->createView($template, $data["body"]);
        $email = $this->container->get("email");

        $email->subject($data["subject"]);
        $email->from($data["from"]);
        $email->to($data["to"]);
        $email->html($view);

        $mailer = $this->container->get("mailer");
        
        try {
            $mailer->send($email);
        } catch(TransportExceptionInterface $transportException) {
            $error = preg_replace("/(\n)(.*)/", "$1", $transportException->getMessage());
            $error = preg_replace("/\n/", "", $error);
            
            return $error;
        }

        return null;
    }

    private function createView(string $template, array $data): string
    {
        $twig = $this->container->get("twig");
        return $twig->render($template, $data);
    }
}
