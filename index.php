<?php

define('WEBROOT_PATH', __DIR__);
define('PROJECT_KEY', 'doc');
require dirname(dirname(__DIR__)).'/ngn/init/web-standalone.php';
print (new DefaultRouter(['disableSession' => true]))->dispatch()->getOutput();