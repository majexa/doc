<?php

class DocMethodsPhp extends ArrayAccesseble {

  function __construct($class, $onlyApiDocs = true) {
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
        if ($onlyApiDocs and !strstr($comment, '@api')) continue;
        $methodName = $method->getName();
        if (in_array($methodName, $methods)) continue;
        $methods[] = $methodName;
        $v = [
          'class' => $_class,
          'method' => $methodName,
          'title' => ClassCore::getDocComment($comment),
        ];
        $r[] = $v;
      }
    }
    $this->r = $r;
  }

}