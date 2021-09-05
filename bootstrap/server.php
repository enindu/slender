<?php

date_default_timezone_set($_ENV["settings"]["timezone"]);
session_start([
    "name"            => $_ENV["session"]["name"],
    "cookie_lifetime" => $_ENV["session"]["lifetime"],
    "cookie_path"     => $_ENV["session"]["path"],
    "cookie_domain"   => $_ENV["session"]["domain"],
    "cookie_secure"   => $_ENV["session"]["secure"],
    "cookie_httponly" => $_ENV["session"]["http_only"],
    "cookie_samesite" => $_ENV["session"]["same_site"],
    "sid_length"      => $_ENV["session"]["sid_length"]
]);
