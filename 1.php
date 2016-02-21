<?php

define('WEBROOT_PATH', __DIR__);
require __DIR__.'/site/config/constants/core.php';
require dirname(dirname(__DIR__)).'/ngn/init/web-standalone.php';

sendHeader();
$file = NGN_ENV_PATH.'/ci/Ci.class.php';
$c = file_get_contents($file);
foreach (ClassCore::getDocComments($c, 'doc') as $v) {
  $mdFile = DATA_PATH.'/docTpl/'.$v['path'].'.md';
  if (!file_exists($mdFile)) {
    throw new Exception("The path '{$v['path']}' used in doc comment of file $file does not exists");
  }
  file_put_contents(DATA_PATH.'/sourceDocs/'.$v['path'].'.md', $v['text']."\n\n", FILE_APPEND);
}
