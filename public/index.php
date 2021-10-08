<?php

$basePath = __DIR__ . "/../";

require_once $basePath . "vendor/autoload.php";

require_once $basePath . "settings/app.php";
require_once $basePath . "settings/framework.php";
require_once $basePath . "settings/session.php";
require_once $basePath . "settings/database.php";
require_once $basePath . "settings/view.php";
require_once $basePath . "settings/email.php";

require_once $basePath . "bootstrap/server.php";
require_once $basePath . "bootstrap/containers.php";
require_once $basePath . "bootstrap/framework.php";
