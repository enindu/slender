<?php

$library = function(): Swift_Mailer {
    $swiftSmtpTransport = new Swift_SmtpTransport();

    $swiftSmtpTransport->setHost($_ENV["swift-mailer"]["host"]);
    $swiftSmtpTransport->setPort($_ENV["swift-mailer"]["port"]);
    $swiftSmtpTransport->setUsername($_ENV["swift-mailer"]["username"]);
    $swiftSmtpTransport->setPassword($_ENV["swift-mailer"]["password"]);
    $swiftSmtpTransport->setEncryption($_ENV["swift-mailer"]["encryption"]);

    return new Swift_Mailer($swiftSmtpTransport);
};

$container->set("swift-mailer", $library);
