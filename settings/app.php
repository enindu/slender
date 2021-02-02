<?php

use Twig\Template;

$_ENV = [
  "app" => [
    "name"        => "Slender",
    "description" => "Developer-friendly, low-level, rapid web development environment, based on Slim framework",
    "keywords"    => "php, slender, slim, twig, eloquent",
    "version"     => "0.3.0-dev",
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
  "database" => [
    "driver"    => "mysql",
    "host"      => "localhost",
    "database"  => "slender",
    "username"  => "root",
    "password"  => "root",
    "charset"   => "utf8",
    "collation" => "utf8_unicode_ci",
    "prefix"    => ""
  ]
];
