<?php

if (preg_match('/\.(?:png|jpg|jpg|gif|css|js).*$/', $_SERVER["REQUEST_URI"])) {
  if (preg_match('/^\\/i\\//', $_SERVER["REQUEST_URI"])) {
    require dirname(dirname(__DIR__)).'/ngn'.$_SERVER["REQUEST_URI"];
  } else {
    return false;
  }
} else { 
  require 'index.php';
}