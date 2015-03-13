<?php

require __DIR__.'/init.php';

define('TEMPLATE_DEBUG', true);
require dirname(dirname(__DIR__)).'/ngn/init/web-standalone.php';
Lib::addFolder(DOC_PATH.'/site/lib');
Lib::addFolder(NGN_ENV_PATH.'/pm/lib');

print (new DefaultRouter(['disableSession' => true]))->dispatch()->getOutput();