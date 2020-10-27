<?php

use Twig\Template;

$_ENV = [
  'app' => [
    'name'        => 'Slender 0.1.4-dev',
    'description' => 'Rapid web development environment based on Slim framework (Slim skeleton)',
    'keywords'    => 'eloquent, php-di, slim, swift-mailer, twig',
    'author'      => 'Enindu Alahapperuma (enindu@gmail.com)',
    'url'         => 'http://localhost',
    'timezone'    => 'Asia/Colombo',
    'charset'     => 'UTF-8',
    'key'         => ''
  ],
  'image' => [
    'driver' => 'gd'
  ],
  'database' => [
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => '',
    'username'  => '',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => ''
  ],
  'view' => [
    'debug'               => true,
    'charset'             => 'UTF-8',
    'base-template-class' => Template::class,
    'cache'               => __DIR__ . '/../cache',
    'auto-reload'         => true,
    'strict-variables'    => true,
    'auto-escape'         => 'html',
    'optimizations'       => -1
  ],
  'mailer' => [
    'host'     => '',
    'port'     => '',
    'username' => '',
    'password' => ''
  ],
  'middleware' => [
    'session' => [
      'lifetime'  => 0,
      'path'      => '/',
      'domain'    => '',
      'secure'    => false,
      'http-only' => false,
      'name'      => 'slender'
    ]
  ]
];
