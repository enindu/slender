<?php

namespace System\View;

use DI\Container;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class Globals extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private Container $container) {}

    public function getGlobals(): array
    {
        return [
            "app"      => $_ENV["app"],
            "settings" => $_ENV["settings"],
            "auth"     => [
                "logged" => isset($_SESSION["account"]),
                "id"     => $_SESSION["account"]["id"] ?? false,
                "role"   => $_SESSION["account"]["role"] ?? false
            ]
        ];
    }
}
