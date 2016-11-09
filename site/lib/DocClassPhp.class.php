<?php

/**
 * Class DocClassPhp
 */
class DocClassPhp extends ArrayAccesseble {

  protected $class;

  function __construct($class) {
    $this->class = $class;
  }

  function __toString() {
    return ClassCore::getDocComment((new ReflectionClass($this->class))->getDocComment(), 'title') ?: '';
  }

  static function parseConstructorOptions($class) {
    $c = file_get_contents(Lib::getPath($class));
    $result = [];
    if (preg_match('/n defineOptions\s*\\(\s*\\)\s*{(.*)}/Usm', $c, $m)) {
      $r = trim(preg_replace('/return\s+\\[(.*)\\];/ms', '$1', $m[1]));
      $r = explode("\n", $r);
      $r = array_map('trim', $r);
      foreach ($r as $v) {
        preg_match("/'([^']+)'\\s*=>\\s*(.*)\\/\\/(.*)/", $v, $m);
        $result[] = [
          'key' => $m[1],
          'default' => rtrim($m[2], ' ,'),
          'title' => trim($m[3])
        ];
      }
    }
    return $result;
  }

}