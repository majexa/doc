<?php

define('WEBROOT_PATH', __DIR__);
require __DIR__.'/site/config/constants/core.php';
require dirname(dirname(__DIR__)).'/ngn/init/web-standalone.php';
print (new DefaultRouter(['disableSession' => true]))->dispatch()->getOutput();