<?php

class CtrlDocJs extends CtrlCommon {

  function action_json_dialog() {
    return $this->jsonFormAction(new Form([
      ['title' => 'Title']
    ], [
      'title' => 'Example Dialog #1'
    ]));
  }

  function action_json_formAjaxSubmit() {
    $this->json['success'] = true;
  }

  function action_json_formUpload() {
    $_SESSION['files'] = $this->req->files;
  }

}