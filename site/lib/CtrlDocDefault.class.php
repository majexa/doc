<?php

class CtrlDocDefault extends CtrlCommon {

  protected function getParamActionN() {
    return 0;
  }

  protected $defaultAction = 'doc';

  function action_doc() {
    $file = DATA_PATH.'/docTpl/tpl.'.(empty($this->req->params[1]) ? 'index' : $this->req->params[1]).'.md';
    $this->d['html'] = DocCore::markdown($file);
  }

  function action_tags() {
    $r = [];
    foreach (glob(DATA_PATH.'/docTpl/*') as $file) {
      $c = file_get_contents($file);
      if (preg_match_all('/{tag (.*)}/', $c, $m)) {
        foreach ($m[1] as $v) {
          $r[] = [
            'title' => $v,
            'name'  => Misc::removePrefix('tpl.', Misc::removeSuffix('.md', basename($file)))
          ];
        }
      }
    }
    foreach ($r as &$v) $v['link'] = '/doc/'.$v['name'];
    $r = Arr::sortByOrderKey($r, 'title');
    $this->d['contentsClass'] = ' tags';
    $this->d['html'] = Tt()->getTpl('cp/links', $r);
  }

  function action_clientSide() {
    $this->d['mainTpl'] = 'jsMain';
    $this->d['tpl'] = 'js/'.$this->req->param(1);
  }

}