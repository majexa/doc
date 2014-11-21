<?php

class DocMultiStaticCli extends CliAccessArgs {
  function prefix() {
    return false;
  }
  function getClasses() {
    return [
      [
        'class' => 'DocCliExample1',
        'name' => 'a'
      ],
      [
        'class' => 'DocCliExample2',
        'name' => 'bee'
      ],
      [
        'class' => 'DocCliExample3',
        'name' => 'sea'
      ],
    ];
  }
  protected function _runner() {
    return 'my-script';
  }
}