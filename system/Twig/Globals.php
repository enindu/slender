<?php

namespace System\Twig;

use DI\Container;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class Globals extends AbstractExtension implements GlobalsInterface
{
  public function __construct(private Container $container) {}

  public function getGlobals(): array
  {
    return [
      "app" => [
        "name"        => $_ENV["app"]["name"],
        "description" => $_ENV["app"]["description"],
        "keywords"    => $_ENV["app"]["keywords"],
        "version"     => $_ENV["app"]["version"],
        "author"      => $_ENV["app"]["author"],
        "domain"      => $_ENV["app"]["domain"],
        "url"         => $_ENV["app"]["url"]
      ]
    ];
  }
}
