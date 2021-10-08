<?php

use System\View\Filters;
use System\View\Functions;
use System\View\Globals;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$view = function() use($container): Environment {
    $path = __DIR__ . "/../views";
    $filesystemLoader = new FilesystemLoader();
    $filesystemLoader->addPath($path);

    $environment = new Environment($filesystemLoader, [
        "debug"               => $_ENV["view"]["debug"],
        "charset"             => $_ENV["view"]["charset"],
        "base_template_class" => $_ENV["view"]["base_template_class"],
        "cache"               => $_ENV["view"]["cache"],
        "auto_reload"         => $_ENV["view"]["auto_reload"],
        "strict_variables"    => $_ENV["view"]["strict_variables"],
        "autoescape"          => $_ENV["view"]["auto_escape"],
        "optimizations"       => $_ENV["view"]["optimizations"]
    ]);

    $globals = new Globals($container);
    $functions = new Functions($container);
    $filters = new Filters($container);

    $environment->addExtension($globals);
    $environment->addExtension($functions);
    $environment->addExtension($filters);

    return $environment;
};

$container->set("view", $view);
