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
                "url"         => $_ENV["app"]["url"],
                "meta"        => $_ENV["app"]["meta"]
            ],
            "auth" => [
                "admin" => [
                    "exists"   => isset($_SESSION["admin"]),
                    "id"       => $_SESSION["admin"]["id"] ?? false,
                    "status"   => $_SESSION["admin"]["status"] ?? false,
                    "username" => $_SESSION["admin"]["username"] ?? false,
                    "role"     => $_SESSION["admin"]["role"] ?? false
                ],
                "user" => [
                    "exists"     => isset($_SESSION["user"]),
                    "id"         => $_SESSION["user"]["id"] ?? false,
                    "status"     => $_SESSION["user"]["status"] ?? false,
                    "first_name" => $_SESSION["user"]["first-name"] ?? false,
                    "last_name"  => $_SESSION["user"]["last-name"] ?? false,
                    "email"      => $_SESSION["user"]["email"] ?? false,
                    "phone"      => $_SESSION["user"]["phone"] ?? false,
                    "role"       => $_SESSION["user"]["role"] ?? false
                ]
            ]
        ];
    }
}
