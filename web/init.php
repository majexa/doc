<?php

define('PROJECT_KEY', 'doc');
define('WEBROOT_PATH', __DIR__);
define('DOC_PATH', dirname(__DIR__));
require __DIR__.'/site/lib/hyperlight/hyperlight.php';
Ngn::addBasePath(NGN_ENV_PATH.'/thm/majexa', 4);
