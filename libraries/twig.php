<?php

use App\Utilities\TwigFilters;
use App\Utilities\TwigFunctions;
use App\Utilities\TwigGlobals;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

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
$environment->addExtension(new TwigGlobals());
$environment->addExtension(new TwigFunctions());
$environment->addExtension(new TwigFilters());
return $environment;
