<?php

class DocMethodsPhp extends ArrayAccesseble {

  function __construct($class, $onlyApiDocs = true, $useInheritClasses = true) {
    $r = [];
    $methods = [];
    $classes = $useInheritClasses ? ClassCore::getAncestors($class) : [$class];
    foreach ($classes as $_class) {
      foreach ((new ReflectionClass($_class))->getMethods() as $method) {
        if (!$useInheritClasses and $_class != $method->class) continue;
        $m = (new ReflectionMethod($_class, $method->getName()));
        $comment = $m->getDocComment();
        $params = [];
        foreach ($m->getParameters() as $p) {
          $params[] = $p->isOptional() ? '[$'.$p->name.']' : '$'.$p->name;
        };
        if (!$comment) continue;
        if ($onlyApiDocs and !strstr($comment, '@api')) continue;
        $methodName = $method->getName();
        if (preg_match('/@api(.*)\n/', $comment, $m)) {
          $comment = preg_replace('/@api(.*)/', '', $comment);
        } else {
          $comment = '';
        }
        if (in_array($methodName, $methods)) continue;
        $methods[] = $methodName;
        $v = [
          'class' => $_class,
          'method' => $methodName,
          'title' => ClassCore::getDocComment($comment),
          //'examples' => ClassCore::getDocComment($comment, 'examples'),
          'api' => $methodName.'('.implode(', ', $params).')',
          'params' => ClassCore::getDocComment($comment, 'param')
        ];
        $r[] = $v;
      }
    }
    $this->r = $r;
  }

}