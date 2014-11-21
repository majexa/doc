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

  function action_clientSide() {
    $this->d['mainTpl'] = 'jsMain';
    $this->d['tpl'] = 'js/'.$this->req->param(1);
  }

}