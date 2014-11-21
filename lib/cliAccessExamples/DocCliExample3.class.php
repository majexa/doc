<?php

class DocCliExample3 {
  protected $name;
  /**
   * В конструкторе есть обязательный параметр. Как быть?
   */
  function __construct($name) {
    $this->name = $name;
  }
  /**
   * Вызывает арифметическую команду
   */
  function one($a, $b = 3) {
    print $this->name.": $a - $b = ".((int)$a - (int)$b)."\n";
  }
  /**
   * Ничего особенного не делает
   */
  function two() {
    print $this->name.": 123\n";
  }
}