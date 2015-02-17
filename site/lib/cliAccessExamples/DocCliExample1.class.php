<?php

class DocCliExample1 {
  /**
   * Вызывает арифметическую команду
   */
  function someCommand($a, $b = 3) {
    print "$a - $b = ".((int)$a - (int)$b)."\n";
  }
}