<?php

use Twig\Template;

$_ENV = [
  "app" => [
    "name"        => "Slender",
    "description" => "Developer-friendly, low-level, rapid web development environment, based on Slim framework",
    "keywords"    => "php, slender, slim, php-di, twig, parsedown",
    "version"     => "v0.3.0-dev",
    "author"      => "Enindu Alahapperuma (me@enindu.com)",
    "url"         => "http://localhost",
    "timezone"    => "Asia/Colombo",
    "charset"     => "UTF-8"
  ],
  "twig" => [
    "debug"               => true,
    "charset"             => "UTF-8",
    "base-template-class" => Template::class,
    "cache"               => __DIR__ . "/../cache/views",
    "auto-reload"         => true,
    "strict-variables"    => true,
    "auto-escape"         => "html",
    "optimizations"       => -1
  ],
  "parsedown" => [
    "safe-mode" => true
  ]
];
