<?php

use Twig\Template;

$_ENV = [
  "app" => [
    "name"        => "Slender",
    "description" => "Developer-friendly, low-level, rapid web development environment, based on Slim framework",
    "keywords"    => "php, slender, slim, php-di, eloquent, twig, swift-mailer, parsedown",
    "version"     => "v0.3.0-dev",
    "author"      => "Enindu Alahapperuma (me@enindu.com)",
    "url"         => "http://localhost",
    "timezone"    => "Asia/Colombo",
    "charset"     => "UTF-8",
    "key"         => "",
    "cookie"      => [
      "admin" => ""
    ]
  ],
  "eloquent" => [
    "driver"    => "mysql",
    "host"      => "localhost",
    "database"  => "slender",
    "username"  => "root",
    "password"  => "root",
    "charset"   => "utf8",
    "collation" => "utf8_unicode_ci",
    "prefix"    => ""
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
  "swift-mailer" => [
    "host"     => "smtp.mailtrap.io",
    "port"     => "465",
    "username" => "f204e4687aa3cb",
    "password" => "3f23587357e4e7"
  ],
  "parsedown" => [
    "safe-mode" => true
  ],
  "image" => [
    "driver" => "gd"
  ],
  "middleware" => [
    "session" => [
      "name"      => "slender",
      "lifetime"  => 0,
      "path"      => "/",
      "domain"    => "localhost",
      "secure"    => false,
      "http-only" => true
    ]
  ]
];
