<?php

class DocBlocksClassPhp extends ArrayAccesseble {

  function __construct($class) {
    $r = [];
    $methods = [];
    foreach (ClassCore::getAncestors($class) as $_class) {
      foreach ((new ReflectionClass($_class))->getMethods() as $method) {
        $m = (new ReflectionMethod($_class, $method->getName()));
        $comment = $m->getDocComment();
        $params = [];
        foreach ($m->getParameters() as $p) {
          $params[] = $p->isOptional() ? '[$'.$p->name.']' : '$'.$p->name;
        };
        if (!$comment) continue;
        if (!strstr($comment, '@api')) continue;
        $methodName = $method->getName();
        if (preg_match('/@api (.*)\n/', $comment, $m)) {
          $comment = preg_replace('/@api (.*)\n/', '', $comment);
          $api = $m[1];
        } else {
          $api = $methodName.'('.implode(', ', $params).')';
        }
        if (in_array($methodName, $methods)) continue;
        $methods[] = $methodName;
        $v = [
          'class' => $_class,
          'method' => $methodName,
          'title' => ClassCore::getDocComment($comment),
          'api' => $api
        ];
        $r[] = $v;
      }
    }
    $this->r = $r;
  }

}