<?php

$_ENV["database"] = [
    "driver"    => "mysql",
    "host"      => "localhost",
    "database"  => "slender",
    "username"  => "root",
    "password"  => "root",
    "charset"   => str_replace("-", "", $_ENV["settings"]["charset"]),
    "collation" => "utf8_unicode_ci",
    "prefix"    => ""
];
