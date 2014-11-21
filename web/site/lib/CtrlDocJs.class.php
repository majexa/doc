<?php

class CtrlDocJs extends CtrlCommon {

  function action_json_dialog() {
    return $this->jsonFormAction(new Form([
      ['title' => 'Title']
    ], [
      'title' => 'Example Dialog #1'
    ]));
  }

}