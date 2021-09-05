<?php

$_ENV["view"] = [
    "debug"               => $_ENV["settings"]["debug"],
    "charset"             => $_ENV["settings"]["charset"],
    "base_template_class" => "\Twig\Template",
    "cache"               => __DIR__ . "/../cache/views",
    "auto_reload"         => true,
    "strict_variables"    => true,
    "auto_escape"         => "html",
    "optimizations"       => -1
];
