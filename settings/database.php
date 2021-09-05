<?php

$_ENV["database"] = [
    "driver"    => "mysql",
    "host"      => "",
    "database"  => "",
    "username"  => "",
    "password"  => "",
    "charset"   => str_replace("-", "", $_ENV["settings"]["charset"]),
    "collation" => "utf8_unicode_ci",
    "prefix"    => ""
];
