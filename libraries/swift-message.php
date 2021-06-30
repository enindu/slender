<?php

$library = function(): Swift_Message {
    return new Swift_Message();
};

$container->set("swift-message", $library);
