<?php

// find lib folders



$c = file_get_contents($file);
foreach (ClassCore::getDocComments($c, 'doc') as $v) {
  $mdFile = DATA_PATH.'/docTpl/'.$v['path'].'.md';
  if (!file_exists($mdFile)) {
    throw new Exception("The path '{$v['path']}' used in doc comment of file $file does not exists");
  }
  file_put_contents(DATA_PATH.'/sourceDocs/'.$v['path'].'.md', $v['text']."\n\n", FILE_APPEND);
}
