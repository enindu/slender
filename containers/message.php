<?php

$container->set('message', function(): Swift_Message {
  return new Swift_Message();
});
