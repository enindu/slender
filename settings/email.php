<?php

$_ENV["email"] = [
    "debug"          => $_ENV["settings"]["debug"],
    "debug_level"    => 0,
    "authentication" => true,
    "encryption"     => $_ENV["settings"]["email"]["encryption"],
    "host"           => $_ENV["settings"]["email"]["host"],
    "port"           => $_ENV["settings"]["email"]["port"],
    "username"       => $_ENV["settings"]["email"]["username"],
    "password"       => $_ENV["settings"]["email"]["password"]
];
