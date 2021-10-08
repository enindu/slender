<?php

use PHPMailer\PHPMailer\PHPMailer;

$email = function(): PHPMailer {
    $phpMailer = new PHPMailer($_ENV["email"]["debug"]);
    $phpMailer->isSMTP();
    $phpMailer->isHTML();
    $phpMailer->SMTPDebug = $_ENV["email"]["debug_level"];
    $phpMailer->SMTPAuth = $_ENV["email"]["authentication"];
    $phpMailer->SMTPSecure = $_ENV["email"]["encryption"];
    $phpMailer->Host = $_ENV["email"]["host"];
    $phpMailer->Port = $_ENV["email"]["port"];
    $phpMailer->Username = $_ENV["email"]["username"];
    $phpMailer->Password = $_ENV["email"]["password"];

    return $phpMailer;
};

$container->set("email", $email);
