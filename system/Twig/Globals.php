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
      ],
      "auth" => [
        "admin" => [
          "logged"   => isset($_SESSION["auth"]["admin"]),
          "id"       => $_SESSION["auth"]["admin"]["id"] ?? false,
          "role_id"  => $_SESSION["auth"]["admin"]["role-id"] ?? false,
          "status"   => $_SESSION["auth"]["admin"]["status"] ?? false,
          "username" => $_SESSION["auth"]["admin"]["username"] ?? false
        ],
        "user" => [
          "logged"     => isset($_SESSION["auth"]["user"]),
          "id"         => $_SESSION["auth"]["user"]["id"] ?? false,
          "role_id"    => $_SESSION["auth"]["user"]["role-id"] ?? false,
          "status"     => $_SESSION["auth"]["user"]["status"] ?? false,
          "first_name" => $_SESSION["auth"]["user"]["first-name"] ?? false,
          "last_name"  => $_SESSION["auth"]["user"]["last-name"] ?? false,
          "email"      => $_SESSION["auth"]["user"]["email"] ?? false,
          "phone"      => $_SESSION["auth"]["user"]["phone"] ?? false
        ]
      ]
    ];
  }
}
