<?php

/**
 * Формрует API с описанием для работы с mootools-классом
 *
 * Пример кода для парсига:
 * var class = new Class({
 *   options: {
 *     a: 'defaultValue', // {string}
 *     b: 'defaultValue', // {Element}
 *     c: 'defaultValue', // {Ngn.SomeClass}
 *   },
 *   initialize: function(el, options) {
 *     ...
 *   }
 * });
 */
class DocBlockMtClassJs extends ArrayAccesseble {

  function __construct($class) {
    $c = file_get_contents((new SflmJsClassPaths)->getAbsPath($class));
    $classRe = str_replace('.', '\\.', $class);
//    $doc = (new NgnJsDoc((new SflmJsClassPaths)->getAbsPath('Ngn.Dialog.Confirm')))->parse();
//    if (($blocks = $doc->docBlocks())) {
//      $this->r['title'] = $blocks[0]['header'];
//    }
    $this->r['class'] = $class;
    if (preg_match('/(?:\s|^)+\\/\\*\\*(.*)\\*\\/\s+'.$classRe.' = new Class/s', $c, $m)) {
      $this->r['descr'] = trim(preg_replace('/^ \\* ?/m', '', $m[1]));
    }
    if (preg_match('/'.$classRe.' = new Class\\(\\{.*Implements: ([^\n]*)\n/sU', $c, $m)) {
      $this->r['implements'] = explode(', ', trim(trim(trim(trim($m[1]), ','), ']'), '['));
    }
    if (preg_match('/initialize: function\\((.*)\\)/i', $c, $m)) {
      $this->r['arguments'] = [];
      foreach (explode(',', $m[1]) as $arg) {
        $arg = trim($arg);
        if ($arg == 'options') {
          $descr = 'Опции';
          $type = 'Object';
        }
        elseif ($arg[0] == 'e') {
          $descr = 'Контейнер';
          $type = 'Element';
        } else {
          $descr = null;
          $type = null;
        }
        $this->r['arguments'][] = [
          'name' => $arg,
          'descr' => $descr,
          'type' => $type,
        ];
      }
    }
    if (preg_match('/'.$classRe.' = new Class\\(\\{.*options:\s\\{(.*)\\}/sU', $c, $m)) {
      $m[1] = preg_replace('/^\s*/m', '', $m[1]); // убираем пробелы с начала строк
      $m[1] = trim($m[1]);
      $options = explode("\n", $m[1]);
      $r = [];
      foreach ($options as $option) {
        $type = null;
        if (preg_match('/\\/\\/(.*)/', $option, $m)) {
          $descr = trim($m[1]);
          if (preg_match('/^\\{(.+)\\}/', $descr, $m)) {
            $descr = preg_replace('/^\\{.+\\}\s*/', '', $descr);
            $type = $m[1];
          }
          $option = preg_replace('/\\/\\/.*/', '', $option);
        } else {
          $descr = null;
        }
        if (!preg_match('/(.*):(.*)/', $option, $m)) continue;
        $r[] = [
          'name' => $m[1],
          'value' => trim(trim($m[2]), ','),
          'descr' => $descr,
          'type' => $type
        ];
      }
      $this->r['options'] = $r;
    }
  }

}