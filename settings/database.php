<?php

$_ENV["database"] = [
    "driver"    => "mysql",
    "host"      => $_ENV["settings"]["database"]["host"],
    "database"  => $_ENV["settings"]["database"]["database"],
    "username"  => $_ENV["settings"]["database"]["username"],
    "password"  => $_ENV["settings"]["database"]["password"],
    "charset"   => "utf8mb4",
    "collation" => "utf8mb4_unicode_ci",
    "prefix"    => ""
];
