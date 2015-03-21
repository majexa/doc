<?php

class DemoDb extends Db {

  function __construct() {
    parent::__construct(DB_USER, DB_PASS, DB_HOST, 'nnweb', DB_CHARSET);
  }

}