<?php

class DocDynamicSub {

  function simple() {}

  function withSub($param) {
    if ($param == 'one') return new CliAccessResultClass('DocCliExample1');
    else return new CliAccessResultClass('DocCliExample3');
  }

}