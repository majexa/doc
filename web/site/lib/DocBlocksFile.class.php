<?php

class DocBlocksFile {

  protected $c, $tokens;

  function __construct($file) {
    $this->c = file_get_contents($file);
    $this->tokens = token_get_all($this->c);
  }

  protected function extendToken($i) {
    if ($this->tokens[$i][0] != T_DOC_COMMENT) return false;
    $method = '';
    //$nextFunctionName = false;
    //$nextBreak = false;
    for ($j = 1; $j < 10; $j++) {
      if (!isset($this->tokens[$i + $j])) break;
      if (!isset($this->tokens[$i + $j][1])) break; // '{'
      $t = $this->tokens[$i + $j];
      $method .= $t[1];
      //if ($nextFunctionName) $nextBreak = true;
      //if ($nextBreak) break;
      //if ($this->tokens[$i + $j][1] == 'function') $nextFunctionName = true;
    }
    if (!$method) return false;
    $token = $this->tokens[$i];
    if (!($comment = $this->parseComment($token[1]))) return false;
    $method = trim(str_replace('function ', '', $method));
    $r = [
      'comment' => $comment,
      'method' => $method,
    ];
    $this->addMethodData($r, 'static');
    $this->addMethodData($r, 'private');
    $this->addMethodData($r, 'protected');
    return $r;
  }

  protected function addMethodData(&$r, $word) {
    if (strstr($r['method'], "$word ")) {
      $d[$word] = true;
      $r['method'] = str_replace("$word ", '', $r['method']);
    } else {
      $d[$word] = false;
    }
  }

  protected function parseComment($c) {
    if (preg_match('/@manual\s+(\w+)/m', $c, $m)) $r['sections'] = Misc::quoted2arr($m[1]);
    if (preg_match('/@cmd\s+/m', $c)) $r['cmd'] = true;
    $c = preg_replace('/\/\*+(.*)\*\//ms', '$1', $c); // убирает открытие и закрытие комментария
    $c = preg_replace('/\s*\*\s*@(.*)/sm', '', $c); // убирает параметры метода
    $c = preg_replace('/\s*\*+\s*(.*)/m', "$1\n", $c); // убираем звёздочки в начале строк
    $r['text'] = trim(trim(trim($c), '*'));
    return $r;
  }

  function methods() {
    $r = [];
    for ($i = 0; $i < count($this->tokens); $i++) {
      if (($token = $this->extendToken($i))) $r[] = $token;
    }
    return $r;
  }

}