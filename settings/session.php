<?php

$_ENV["session"] = [
    "name"       => $_ENV["settings"]["cookie"]["name"]["session"],
    "lifetime"   => 0,
    "path"       => "/",
    "domain"     => $_ENV["settings"]["domain"],
    "secure"     => $_ENV["settings"]["cookie"]["secure"],
    "http_only"  => $_ENV["settings"]["cookie"]["http_only"],
    "same_site"  => $_ENV["settings"]["cookie"]["same_site"],
    "sid_length" => 67
];
