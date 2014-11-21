<?php

class DocCliExample2 {
  /**
   * Вызывает арифметическую команду
   */
  function one($a, $b = 3) {
    print "$a - $b = ".((int)$a - (int)$b)."\n";
  }
  /**
   * Ничего особенного не делает
   */
  function two() {
    print "123\n";
  }
}