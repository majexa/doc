<?php

class DocOpt extends ArrayAccessebleOptions {

  static $requiredOptions = ['name'];

  /**
   * Выводит параметры
   *
   * @options one, two
   */
  function a_printSomething() {
    print $this->options['name'].': '. //
      $this->options['one'].' .. '.$this->options['two']."\n==\n";
  }

  /**
   * Выводит параметры
   *
   * @options one, @variant
   */
  function a_printVariant() {
    print $this->options['name'].': '. //
      $this->options['one'].' .. '.$this->options['variant']."\n==\n";
  }

  static function helpOpt_variant() {
    return [1, 'none'];
  }

}