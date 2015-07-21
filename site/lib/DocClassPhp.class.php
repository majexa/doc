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
    return ClassCore::getDocComment((new ReflectionClass($this->class))->getDocComment());
  }

}