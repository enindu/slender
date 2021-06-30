<?php

use System\Twig\Filters;
use System\Twig\Functions;
use System\Twig\Globals;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

$library = function() use ($container): Environment {
    $filesystemLoader = new FilesystemLoader();

    $filesystemLoader->addPath(__DIR__ . "/../resources/admin/views", "admin");
    $filesystemLoader->addPath(__DIR__ . "/../resources/user/views", "user");
    $filesystemLoader->addPath(__DIR__ . "/../resources/common/views", "common");

    $environment = new Environment($filesystemLoader, [
        "debug"               => $_ENV["twig"]["debug"],
        "charset"             => $_ENV["twig"]["charset"],
        "base_template_class" => $_ENV["twig"]["base-template-class"],
        "cache"               => $_ENV["twig"]["cache"],
        "auto_reload"         => $_ENV["twig"]["auto-reload"],
        "strict_variables"    => $_ENV["twig"]["strict-variables"],
        "autoescape"          => $_ENV["twig"]["auto-escape"],
        "optimizations"       => $_ENV["twig"]["optimizations"]
    ]);

    $intlExtensions = new IntlExtension();
    $globals = new Globals($container);
    $functions = new Functions($container);
    $filters = new Filters($container);

    $environment->addExtension($intlExtensions);
    $environment->addExtension($globals);
    $environment->addExtension($functions);
    $environment->addExtension($filters);

    return $environment;
};

$container->set("twig", $library);
